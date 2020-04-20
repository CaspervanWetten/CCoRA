<?php

namespace Cora\Handlers\Session;

use Cora\Domain\Session\NoSessionException;
use Cora\Domain\User\UserNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Repositories\SessionRepository as SessionRepo;
use Cora\Services\GetSessionService;
use Cora\Views\CurrentSessionViewFactory;
use Exception;

class GetCurrentSession extends AbstractHandler {
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
            $mediaType = $this->getErrorMediaType($request);
            $view = $this->getErrorView($mediaType);
            $view->setException($e);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(404)
                            ->write($view->render());
        } catch (NoSessionException $e) {
            $mediaType = $this->getErrorMediaType($request);
            $view = $this->getErrorView($mediaType);
            $view->setException($e);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(400)
                            ->write($view->render());
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new CurrentSessionViewFactory();
    }
}
