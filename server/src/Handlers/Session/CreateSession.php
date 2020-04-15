<?php

namespace Cora\Handlers\Session;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Repositories\PetrinetRepository as PetriRepo;
use Cora\Repositories\SessionRepository as SessionRepo;
use Cora\Services\StartSessionService;
use Cora\Views\JsonSessionCreatedView;

class CreateSession extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $service = $this->container->get(StartSessionService::class);
        $userRepo = $this->container->get(UserRepo::class);
        $petriRepo = $this->container->get(PetriRepo::class);
        $sessionRepo = $this->container->get(SessionRepo::class);
        $view = new JsonSessionCreatedView();
        $service->start(
            $view,
            $args["id"],
            $args["pid"],
            $sessionRepo,
            $userRepo,
            $petriRepo);
        $response->withHeader("Content-type", $view->getContentType())
                 ->withStatus(201)
                 ->write($view->render());
    }
}
