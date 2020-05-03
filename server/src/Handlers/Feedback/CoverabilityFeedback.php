<?php

namespace Cora\Handlers\Feedback;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractRequestHandler;
use Cora\Domain\Feedback\View\FeedbackViewFactory;
use Cora\Domain\Petrinet\PetrinetRepository as PetrinetRepo;
use Cora\Domain\Session\InvalidSessionException;
use Cora\Domain\Session\NoSessionLogException;
use Cora\Domain\Session\SessionRepository as SessionRepo;
use Cora\Services\GetFeedbackService;

class CoverabilityFeedback extends AbstractRequestHandler {
    public function handle(Request $request, Response $response, $args) {
        try {
            $userId      = $request->getParsedBodyParam("user_id", NULL);
            $sessionId   = $request->getParsedBodyParam("session_id", NULL);
            $graph       = $request->getParsedBodyParam("graph", NULL);
            $petriRepo   = $this->container->get(PetrinetRepo::class);
            $sessionRepo = $this->container->get(SessionRepo::class);
            $service     = $this->container->get(GetFeedbackService::class);
            $mediaType   = $this->getMediaType($request);
            $view        = $this->getView($mediaType);
            $service->get(
                $view,
                $graph,
                $userId,
                $sessionId,
                $petriRepo,
                $sessionRepo
            );
            return $response->withHeader("Content-type", $mediaType)
                ->withStatus(200)
                ->write($view->render());
        } catch(InvalidSessionException |
                NoSessionLogException $e) {
            return $this->fail($request, $response, $e, 404);
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new FeedbackViewFactory();
    }
}
