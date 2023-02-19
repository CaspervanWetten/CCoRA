<?php

namespace Cora\Handler\User;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

use Cora\Handler\AbstractHandler;
use Cora\Service\RegisterUserService;
use Cora\View\Factory\ViewFactory;
use Cora\View\Factory\UserCreatedViewFactory;

class RegisterUser extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $body = $request->getParsedBody();

        if (!isset($body['name']))
            throw new HttpBadRequestException($request, "No name supplied");

        $service = $this->container->get(RegisterUserService::class);
        $registration = $service->register($body['name']);

        if ($registration->failed())
            throw new HttpBadRequestException(
                $request, $registration->getErrorMessage());

        $view = $this->getView();
        $view->setUserId($registration->getUserId());
        $response->getBody()->write($view->render());
        return $response->withStatus(201);
    }

    protected function getViewFactory(): ViewFactory {
        return new UserCreatedViewFactory();
    }
}
