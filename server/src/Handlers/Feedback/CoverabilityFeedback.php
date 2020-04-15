<?php

namespace Cora\Handlers\Feedback;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Repositories\SessionRepository as SessionRepo;
use Cora\Services\GetFeedbackService;
use Cora\Views\JsonFeedbackView;

class CoverabilityFeedback extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $graph = $request->getBody()->getContents();
        $userRepo = $this->container->get(UserRepo::class);
        $petriRepo = $this->container->get(PetrinetRepo::class);
        $sessionRepo = $this->container->get(SessionRepo::class);
        $service = $this->container->get(GetFeedbackService::class);
        $view = new JsonFeedbackView();
        $service->get(
            $view,
            $graph,
            $args["user_id"],
            $args["petrinet_id"],
            $args["session_id"],
            $userRepo,
            $petriRepo,
            $sessionRepo);
        return $response->withHeader("Content-type", $view->getContentType())
                        ->withStatus(200)
                        ->write($view->render());
    }
}
