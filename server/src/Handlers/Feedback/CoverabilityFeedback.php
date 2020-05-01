<?php

namespace Cora\Handlers\Feedback;

use Cora\Domain\Feedback\NoInitialMarkingException;
use Cora\Domain\Feedback\NoPetrinetException;
use Cora\Domain\Feedback\NoSessionException;
use Cora\Domain\Feedback\NoUserException;
use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractRequestHandler;
use Cora\Domain\Feedback\View\FeedbackViewFactory;
use Cora\Domain\Petrinet\Marking\MarkingNotFoundException;
use Cora\Domain\Petrinet\PetrinetNotFoundException;
use Cora\Domain\Petrinet\PetrinetRepository as PetrinetRepo;
use Cora\Domain\Session\InvalidSessionException;
use Cora\Domain\Session\SessionRepository as SessionRepo;
use Cora\Domain\User\Exception\UserNotFoundException;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Services\GetFeedbackService;

class CoverabilityFeedback extends AbstractRequestHandler {
    public function handle(Request $request, Response $response, $args) {
        try {
            $body        = $request->getParsedBody();
            $userId      = isset($body["user_id"]) ? $body["user_id"] : NULL;
            $petrinetId  = isset($body["petrinet_id"]) ? $body["petrinet_id"] : NULL;
            $sessionId   = isset($body["session_id"]) ? $body["session_id"] : NULL;
            $graph       = isset($body["graph"]) ? $body["graph"] : NULL;
            $markingId   = isset($body["initial_marking_id"]) ?
                $body["initial_marking_id"] : NULL;
            $userRepo    = $this->container->get(UserRepo::class);
            $petriRepo   = $this->container->get(PetrinetRepo::class);
            $sessionRepo = $this->container->get(SessionRepo::class);
            $service     = $this->container->get(GetFeedbackService::class);
            $mediaType   = $this->getMediaType($request);
            $view        = $this->getView($mediaType);
            $service->get(
                $view,
                $graph,
                $userId,
                $petrinetId,
                $sessionId,
                $markingId,
                $userRepo,
                $petriRepo,
                $sessionRepo
            );
            return $response->withHeader("Content-type", $mediaType)
                ->withStatus(200)
                ->write($view->render());
        } catch (NoUserException |
                 NoPetrinetException |
                 NoSessionException |
                 NoInitialMarkingException |
                 InvalidSessionException $e) {
            return $this->fail($request, $response, $e, 400);
        } catch (UserNotFoundException |
                 PetrinetNotFoundException |
                 MarkingNotFoundException $e) {
            return $this->fail($request, $response, $e, 404);
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new FeedbackViewFactory();
    }
}
