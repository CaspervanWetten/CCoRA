<?php

namespace Cora\Handlers\Petrinet;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Converters\LolaToPetrinet;
use Cora\Converters\PetrinetTranslator;
use Cora\Utils\FileUtils;
use Cora\Utils\FileUploadUtils;

use Exception;

class RegisterPetrinet extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        if (!isset($args["id"]))
            throw new Exception("No user id provided");
        $userId = filter_var($args["id"], FILTER_SANITIZE_NUMBER_INT);
        $userRepo = $this->container->get(UserRepo::class);
        if (!$userRepo->userExists("id", $userId))
            throw new Exception("No user with this id exists");
        $files = $request->getUploadedFiles();
        if (!isset($files["petrinet"]))
            throw new Exception("No petrinet uploaded");
        $file = $files["petrinet"];
        $error = $file->getError();
        if ($error != UPLOAD_ERR_OK)
            throw new Exception(FileUploadUtils::getErrorMessage($error));

        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        if ($extension != "lola")
            throw new Exception("Only files with a lola extension are accepted");

        try {
            $fileName = USER_FOLDER .
                      DIRECTORY_SEPARATOR .
                      $userId .
                      DIRECTORY_SEPARATOR .
                      date("Y-m-d-H:i:s");
            $userDir = USER_FOLDER . DIRECTORY_SEPARATOR . $userId;
            FileUtils::mkdir($userDir, 0711);
            $file->moveTo($fileName);

            $converter = new LolaToPetrinet($fileName);
            $petrinet = $converter->convert();

            if (is_null($petrinet->getInitial())) {
                $message = "No initial marking supplied for the Petri net. "
                         . "Reachability and Coverability analysis are "
                         . "therefore not possible.";
                throw new Exception($message);
            }
            
            $translate = true;
            if ($translate) {
                $translator = new PetrinetTranslator($petrinet);
                $petrinet = $translator->convert();
            }

            $petrinetRepo = $this->container->get(PetrinetRepo::class);
            $petrinetId = $petrinetRepo->savePetrinet($petrinet, $userId);

            $router = $this->container->get("router");
            $response = $response->withJson([
                "petrinetId" => $petrinetId,
                "petrinetUrl" => $router->pathFor("getPetrinet",
                                                  ["id" => $petrinetId])
            ]);
        } finally {
            unlink($fileName);
            rmdir($userDir);
        }
        return $response;
    }
}
