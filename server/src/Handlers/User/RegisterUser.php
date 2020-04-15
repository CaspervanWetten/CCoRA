<?php

namespace Cora\Handlers\User;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Handlers\AbstractHandler;
use Cora\Services\RegisterUserService;
use Cora\Views\JsonUserCreatedView;
use Exception;

class RegisterUser extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $body = $request->getParsedBody();
        if (!isset($body["name"]))
            throw new Exception("No name supplied");
        $repo = $this->container->get(UserRepo::class);
        $view = new JsonUserCreatedView();
        $service = $this->container->get(RegisterUserService::class);
        $service->register($view, $repo, $body["name"]);
        return $response->withHeader("Content-type", $view->getContentType())
                        ->withStatus(201)
                        ->write($view->render());
    }
}
