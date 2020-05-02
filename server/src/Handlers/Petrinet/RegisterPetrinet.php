<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\Exception\UserNotFoundException;
use Cora\Domain\Petrinet\PetrinetRepository as PetrinetRepo;
use Cora\Domain\Petrinet\View\PetrinetCreatedViewFactory;
use Cora\Handlers\AbstractRequestHandler;
use Cora\Handlers\BadRequestException;
use Cora\Services\RegisterPetrinetService;

class RegisterPetrinet extends AbstractRequestHandler {
    public function handle(Request $request, Response $response, $args) {
        try {
            if (is_null($userId = $request->getParsedBodyParam("user_id", NULL)))
                throw new BadRequestException("No user id");
            $files = $request->getUploadedFiles();
            if (!isset($files["petrinet"]))
                throw new BadRequestException("No Petri net uploaded");
            $file         = $files["petrinet"];
            $petrinetRepo = $this->container->get(PetrinetRepo::class);
            $userRepo     = $this->container->get(UserRepo::class);
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
