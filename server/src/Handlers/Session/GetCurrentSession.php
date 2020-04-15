<?php

namespace Cora\Handlers\Session;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Repositories\SessionRepository as SessionRepo;
use Cora\Services\GetSessionService;
use Exception;

class GetCurrentSession extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        if (!isset($args["id"]))
            throw new Exception("No id supplied");
        $userRepo = $this->container->get(UserRepo::class);
        $sessionRepo = $this->container->get(SessionRepo::class);
        $service = $this->container->get(GetSessionService::class);
        $session = $service->get($args["id"], $sessionRepo, $userRepo);
        return $response->withJson([
            "session_id" => $session]);
    }
}
