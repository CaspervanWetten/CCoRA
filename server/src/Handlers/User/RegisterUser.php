<?php

namespace Cora\Handlers\User;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\UserRepository as UserRepo;
use Cora\Validation;

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
        $validator = $this->getValidator();
        if (!$validator->validate($name))
            throw new Exception($validator->getError());
        $id = $repo->saveUser($name);
        $router = $this->container->get("router");
        $selfUrl = $router->pathFor('getUser', ["id" => $id]);
        return $response->withJson(["id" => $id, "selfUrl" => $selfUrl], 201);
    }

    protected function getValidator() {
        $minRule = new Validation\MinLengthRule(
            4,
            "Your username is too short. A minimum of four characters is required"
        );
        $maxRule = new Validation\MaxLengthRule(
            20,
            "Your username is too long. You may use up to twenty characters"
        );
        $validator = new Validation\RuleValidator([$minRule, $maxRule]);
        return $validator;
    }
}
        
