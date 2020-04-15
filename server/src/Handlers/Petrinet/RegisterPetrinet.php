<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Services\RegisterPetrinetService;

use Exception;

class RegisterPetrinet extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $userId = $args["id"];
        $userRepo = $this->container->get(UserRepo::class);
        $files = $request->getUploadedFiles();
        if (!isset($files["petrinet"]))
            throw new Exception("No Petri net uploaded");
        $file = $files["petrinet"];
        $petrinetRepo = $this->container->get(PetrinetRepo::class);
        $service = $this->container->get(RegisterPetrinetService::class);
        $petrinetId = $service->register($userId, $file, $userRepo, $petrinetRepo);
        return $response->withJson(["petrinet_id" => $petrinetId]);
    }
}
