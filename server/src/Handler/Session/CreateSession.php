<?php

namespace Cora\Handler\Session;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

use Cora\Handler\AbstractHandler;
use Cora\Service\StartSessionService;
use Cora\View\Factory\ViewFactory;
use Cora\View\Factory\SessionCreatedViewFactory;

class CreateSession extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $parsedBody= $request->getParsedBody();

        $userId = $parsedBody["user_id"] ?? NULL;
        if (is_null($userId))
            throw new HttpBadRequestException(
                $request, "No user id provided");

        $petrinetId = $parsedBody["petrinet_id"] ?? NULL;
        if (is_null($petrinetId))
            throw new HttpBadRequestException(
                $request, "No Petri net id provided");

        $markingId = $parsedBody["marking_id"] ??  NULL;
        if (is_null($markingId))
            throw new HttpBadRequestException(
                $request, "No marking id provided");

        $service = $this->container->get(StartSessionService::class);
        $session = $service->start($userId, $petrinetId, $markingId);

        $view = $this->getView();
        $view->setSession($session);
        $response->getBody()->write($view->render());
        return $response->withStatus(201);
    }

    protected function getViewFactory(): ViewFactory {
        return new SessionCreatedViewFactory();
    }
}
