<?php

namespace Cora\Handler\User;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handler\AbstractHandler;
use Cora\Service\GetUsersService;
use Cora\View\Factory\ViewFactory;
use Cora\View\Factory\UsersViewFactory;

class GetUsers extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $page  = $args["page"]  ?? NULL;
        $limit = $args["limit"] ?? NULL;

        $service = $this->container->get(GetUsersService::class);
        $users = $service->getUsers($page, $limit);

        $view = $this->getView();
        $view->setUsers($users);
        $response->getBody()->write($view->render());
        return $response;
    }

    protected function getViewFactory(): ViewFactory {
        return new UsersViewFactory();
    }
}
