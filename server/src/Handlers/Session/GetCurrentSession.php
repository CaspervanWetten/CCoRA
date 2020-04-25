<?php

namespace Cora\Handlers\Session;

use Cora\Domain\Session\NoSessionException;
use Cora\Domain\User\UserNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractRequestHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\Session\SessionRepository as SessionRepo;
use Cora\Services\GetSessionService;
use Cora\Domain\Session\Views\CurrentSessionViewFactory;
use Exception;

class GetCurrentSession extends AbstractRequestHandler {
    public function handle(Request $request, Response $response, $args) {
        if (!isset($args["id"]))
            throw new Exception("No id supplied");
        try {
            $userRepo    = $this->container->get(UserRepo::class);
            $sessionRepo = $this->container->get(SessionRepo::class);
            $mediaType   = $this->getMediaType($request);
            $view        = $this->getView($mediaType);
            $service     = $this->container->get(GetSessionService::class);
            $service->get($view, $args["id"], $sessionRepo, $userRepo);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(200)
                            ->write($view->render());
        } catch (UserNotFoundException $e) {
            return $this->fail($request, $response, $e, 404);
        } catch (NoSessionException $e) {
            return $this->fail($request, $response, $e, 400);
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new CurrentSessionViewFactory();
    }
}
