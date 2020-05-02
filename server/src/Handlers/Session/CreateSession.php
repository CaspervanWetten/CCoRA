<?php

namespace Cora\Handlers\Session;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\Petrinet\PetrinetNotFoundException;
use Cora\Domain\Petrinet\PetrinetRepository as PetriRepo;
use Cora\Domain\Session\SessionRepository as SessionRepo;
use Cora\Domain\Session\View\SessionCreatedViewFactory;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\UserNotFoundException;
use Cora\Handlers\AbstractRequestHandler;
use Cora\Handlers\BadRequestException;
use Cora\Services\StartSessionService;

class CreateSession extends AbstractRequestHandler {
    public function handle(Request $request, Response $response, $args) {
        try {
            $userId = $request->getParsedBodyParam("user_id", NULL);
            if (is_null($userId))
                throw new BadRequestException("No user id provided");
            $netId = $request->getParsedBodyParam("petrinet_id", NULL);
            if (is_null($netId))
                throw new BadRequestException("No Petri net id provided");
            $mediaType   = $this->getMediaType($request);
            $service     = $this->container->get(StartSessionService::class);
            $userRepo    = $this->container->get(UserRepo::class);
            $petriRepo   = $this->container->get(PetriRepo::class);
            $sessionRepo = $this->container->get(SessionRepo::class);
            $view        = $this->getView($mediaType);
            $service->start(
                $view,
                $userId,
                $netId,
                $sessionRepo,
                $userRepo,
                $petriRepo
            );
            $response->withHeader("Content-type", $mediaType)
                     ->withStatus(201)
                     ->write($view->render());
        } catch (UserNotFoundException | PetrinetNotFoundException $e) {
            return $this->fail($request, $response, $e, 404);
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new SessionCreatedViewFactory();
    }
}
