<?php

namespace Cora\Handlers\User;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handlers\AbstractHandler;
use Cora\Services\GetUserService;
use Cora\Views\JsonUserView;
use Cora\Views\JsonErrorView;
use Cora\User\UserNotFoundException;
use Cora\User\UserRepository as UserRepo;

use Exception;

class GetUser extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $id = $args["id"];
        if (!isset($id))
            throw new Exception("No id given");
        try {
            $service = $this->container->get(GetUserService::class);
            $repo = $this->container->get(UserRepo::class);
            $view = new JsonUserView();
            $service->getUser($view, $repo, $id);
            return $response->withHeader("Content-type", $view->getContentType())
                            ->write($view->toString());
        } catch (UserNotFoundException $e) {
            $view = new JsonErrorView($e);
            return $response->withHeader("Content-type", $view->getContentType())
                            ->withStatus(404)
                            ->write($view->toString());
        }
    }
}
