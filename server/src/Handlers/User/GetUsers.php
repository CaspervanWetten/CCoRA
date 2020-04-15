<?php

namespace Cora\Handlers\User;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Handlers\AbstractHandler;
use Cora\Services\GetUsersService;

class GetUsers extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $repo    = $this->container->get(UserRepo::class);
        $service = $this->container->get(GetUsersService::class);
        $users   = $service->getUsers($repo, $args["page"], $args["limit"]);

        return $response->withJson($users);
    }
}
