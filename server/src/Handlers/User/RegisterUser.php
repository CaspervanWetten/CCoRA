<?php

namespace Cora\Handlers\User;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\UserRepository as UserRepo;
use Cora\Validator\Validator;

use Exception;

class RegisterUser extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $body = $request->getParsedBody();
        if (!isset($body["name"]))
            throw new Exception("No name supplied");
        $name = filter_var($body["name"], FILTER_SANITIZE_STRING);
        $repo = $this->container->get(UserRepo::class);
        if ($repo->userExists("name", $name))
            throw new Exception("A User with this name already exists");
        $validator = new Validator($this->getConfig());
        if (!$validator->validate($name))
            throw new Exception($validator->getError());
        $id = $repo->saveUser($name);
        $router = $this->container->get("router");
        $selfUrl = $router->pathFor('getUser', ["id" => $id]);
        return $response->withJson(["id" => $id, "selfUrl" => $selfUrl], 201);
    }

    protected function getConfig() {
        return [
            "min_length" => [
                "argument" => 4,
                "message" => "Your username is too short. A minimum of four characters is required"
            ],
            "max_length" => [
                "argument" => 20,
                "message" => "Your username is too long. You may use up to twenty characters"
            ]
        ];
    }
}
        
