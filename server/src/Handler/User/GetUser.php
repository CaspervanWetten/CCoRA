<?php

namespace Cora\Handler\User;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

use Cora\Handler\AbstractHandler;
use Cora\Service\GetUserService;
use Cora\View\Factory\ViewFactory;
use Cora\View\Factory\UserViewFactory;

class GetUser extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $id = $args['id'];
        if (!isset($id))
            throw new HttpBadRequestException($request, 'No id given');

        $service = $this->container->get(GetUserService::class);
        $user = $service->getUser($id);

        if (is_null($user)) throw new HttpNotFoundException(
            $request, "No user found for this id");

        $view = $this->getView();
        $view->setUser($user);
        $response->getBody()->write($view->render());
        return $response;
    }

    protected function getViewFactory(): ViewFactory {
        return new UserViewFactory();
    }
}
