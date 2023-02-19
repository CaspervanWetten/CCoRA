<?php

namespace Cora\Handler\Feedback;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

use Cora\Handler\AbstractHandler;
use Cora\Service\GetFeedbackService;
use Cora\View\Factory\ViewFactory;
use Cora\View\Factory\FeedbackViewFactory;

class CoverabilityFeedback extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();

        $userId = $parsedBody["user_id"] ?? NULL;
        if (is_null($userId))
            throw new HttpBadRequestException(
                $request, "No user id provided");

        $sessionId = $parsedBody["session_id"] ?? NULL;
        if (is_null($sessionId))
            throw new HttpBadRequestException(
                $request, "No session id provided");

        $graph = $parsedBody["graph"] ?? NULL;
        if (is_null($graph))
            throw new HttpBadRequestException(
                $request, "No graph provided");

        $service = $this->container->get(GetFeedbackService::class);
        $result = $service->get($graph, $userId, $sessionId);

        $view = $this->getView();
        $view->setFeedback($result);
        $response->getBody()->write($view->render());
        return $response;
    }

    protected function getViewFactory(): ViewFactory {
        return new FeedbackViewFactory();
    }
}
