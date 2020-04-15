<?php

namespace Cora\Handlers\User;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\UniqueUserRule;
use Cora\Handlers\AbstractHandler;
use Cora\Validation;

use Exception;

class RegisterUser extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $body = $request->getParsedBody();
        if (!isset($body["name"]))
            throw new Exception("No name supplied");
        $name = filter_var($body["name"], FILTER_SANITIZE_STRING);
        $repo = $this->container->get(UserRepo::class);
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
            "Your username is too short. A minimum of four characters is required");
        $maxRule = new Validation\MaxLengthRule(
            20,
            "Your username is too long. You may use up to twenty characters");
        $regexRule = new Validation\RegexRule(
            "/^\w+$/",
            "Your username contains illegal characters.");
        $uniqueRule = new UniqueUserRule(
            $this->container->get(UserRepo::class));
        $validator = new Validation\RuleValidator([
            $minRule,
            $maxRule,
            $regexRule,
            $uniqueRule
        ]);
        return $validator;
    }
}
