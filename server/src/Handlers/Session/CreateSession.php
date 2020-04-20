<?php

namespace Cora\Handlers\Session;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\UserNotFoundException;
use Cora\Repositories\PetrinetNotFoundException;
use Cora\Repositories\PetrinetRepository as PetriRepo;
use Cora\Repositories\SessionRepository as SessionRepo;
use Cora\Services\StartSessionService;
use Cora\Views\SessionCreatedViewFactory;

class CreateSession extends AbstractHandler {
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
            $mediaType = $this->getErrorMediaType($request);
            $view = $this->getErrorView($mediaType);
            $view->setException($e);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(404)
                            ->write($view->render());
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new SessionCreatedViewFactory();
    }
}
