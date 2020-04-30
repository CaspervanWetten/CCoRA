<?php

namespace Cora\Handlers\Petrinet;

use Cora\Domain\User\UserNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\Petrinet\PetrinetRepository as PetrinetRepo;
use Cora\Domain\Petrinet\View\PetrinetCreatedViewFactory;
use Cora\Handlers\AbstractRequestHandler;
use Cora\Services\RegisterPetrinetService;
use Exception;

class RegisterPetrinet extends AbstractRequestHandler {
    public function handle(Request $request, Response $response, $args) {
        $userId = $args["id"];
        $userRepo = $this->container->get(UserRepo::class);
        $files = $request->getUploadedFiles();
        if (!isset($files["petrinet"]))
            throw new Exception("No Petri net uploaded");
        $file         = $files["petrinet"];

        try {
            $petrinetRepo = $this->container->get(PetrinetRepo::class);
            $mediaType    = $this->getMediaType($request);
            $view         = $this->getView($mediaType);
            $service      = $this->container->get(RegisterPetrinetService::class);
            $service->register(
                $view,
                $userId,
                $file,
                $userRepo,
                $petrinetRepo
            );
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(201)
                            ->write($view->render());
        } catch (UserNotFoundException $e) {
            return $this->fail($request, $response, $e, 404);
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new PetrinetCreatedViewFactory();
    }
}
