<?php

namespace Cora\Handlers\User;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Handlers\AbstractHandler;
use Cora\Services\GetUsersService;
use Cora\Views\JsonUsersView;

class GetUsers extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $repo    = $this->container->get(UserRepo::class);
        $view    = new JsonUsersView();
        $service = $this->container->get(GetUsersService::class);
        $service->getUsers($view, $repo, $args["page"], $args["limit"]);

        return $response->withHeader("Content-type", $view->getContentType())
                        ->withStatus(200)
                        ->write($view->render());
    }
}
