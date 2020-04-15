<?php

namespace Cora\Handlers\Session;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Repositories\PetrinetRepository as PetriRepo;
use Cora\Repositories\SessionRepository as SessionRepo;
use Cora\Services\StartSessionService;

class CreateSession extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $service = $this->container->get(StartSessionService::class);
        $userRepo = $this->container->get(UserRepo::class);
        $petriRepo = $this->container->get(PetriRepo::class);
        $sessionRepo = $this->container->get(SessionRepo::class);
        $sessionId = $service->start(
            $args["id"],
            $args["pid"],
            $sessionRepo,
            $userRepo,
            $petriRepo);
        return $response->withJson(["session_id" => $sessionId]);
    }
}
