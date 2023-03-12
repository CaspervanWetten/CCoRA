<?php

namespace Cora\Handler\Session;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpBadRequestException;

use Cora\Handler\AbstractHandler;
use Cora\Service\GetSessionService;
use Cora\View\Factory\ViewFactory;
use Cora\View\Factory\CurrentSessionViewFactory;

class GetCurrentSession extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $userId = $parsedBody["user_id"] ?? NULL;
        if (is_null($userId))
            throw new HttpBadRequestException($request, "No user id supplied");

        $service = $this->container->get(GetSessionService::class);
        $result = $service->get($userId);

        if (is_null($result))
            throw new HttpNotFoundException($request, "No session found");

        $view = $this->getView();
        $view->setSession($result);
        $response->getBody()->write($view->render());
        return $response;
    }

    protected function getViewFactory(): ViewFactory {
        return new CurrentSessionViewFactory();
    }
}
