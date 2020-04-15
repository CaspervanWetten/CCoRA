<?php

namespace Cora\Handlers\User;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Handlers\AbstractHandler;
use Cora\Services\RegisterUserService;
use Exception;

class RegisterUser extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $body = $request->getParsedBody();
        if (!isset($body["name"]))
            throw new Exception("No name supplied");
        $repo = $this->container->get(UserRepo::class);
        $service = $this->container->get(RegisterUserService::class);
        $id = $service->register($repo, $body["name"]);
        $router = $this->container->get("router");
        $selfUrl = $router->pathFor('getUser', ["id" => $id]);
        return $response->withJson(["id" => $id, "selfUrl" => $selfUrl], 201);
    }
}
