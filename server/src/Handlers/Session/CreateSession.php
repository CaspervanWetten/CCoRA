<?php

namespace Cora\Handlers\Session;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractRequestHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\UserNotFoundException;
use Cora\Domain\Systems\Petrinet\PetrinetNotFoundException;
use Cora\Domain\Systems\Petrinet\PetrinetRepository as PetriRepo;
use Cora\Domain\Session\SessionRepository as SessionRepo;
use Cora\Services\StartSessionService;
use Cora\Domain\Session\Views\SessionCreatedViewFactory;

class CreateSession extends AbstractRequestHandler {
    public function handle(Request $request, Response $response, $args) {
        try {
            $mediaType   = $this->getMediaType($request);
            $service     = $this->container->get(StartSessionService::class);
            $userRepo    = $this->container->get(UserRepo::class);
            $petriRepo   = $this->container->get(PetriRepo::class);
            $sessionRepo = $this->container->get(SessionRepo::class);
            $view        = $this->getView($mediaType);
            $service->start(
                $view,
                $args["id"],
                $args["pid"],
                $sessionRepo,
                $userRepo,
                $petriRepo
            );
            $response->withHeader("Content-type", $mediaType)
                     ->withStatus(201)
                     ->write($view->render());
        } catch (UserNotFoundException | PetrinetNotFoundException $e) {
            return $this->fail($request, $response, $e, 404);
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new SessionCreatedViewFactory();
    }
}
