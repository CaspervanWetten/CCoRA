<?php

namespace Cora\Handlers\Session;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\Exception\UserNotFoundException;
use Cora\Domain\Session\SessionRepository as SessionRepo;
use Cora\Domain\Session\View\CurrentSessionViewFactory;
use Cora\Domain\Session\NoSessionException;
use Cora\Handlers\AbstractRequestHandler;
use Cora\Handlers\BadRequestException;
use Cora\Services\GetSessionService;

class GetCurrentSession extends AbstractRequestHandler {
    public function handle(Request $request, Response $response, $args) {
        try {
            $userId = $request->getParsedBodyParam("user_id", NULL);
            if (is_null($userId))
                throw new BadRequestException("No user id supplied");
            $userRepo    = $this->container->get(UserRepo::class);
            $sessionRepo = $this->container->get(SessionRepo::class);
            $mediaType   = $this->getMediaType($request);
            $view        = $this->getView($mediaType);
            $service     = $this->container->get(GetSessionService::class);
            $service->get(
                $view,
                $userId,
                $sessionRepo,
                $userRepo
            );
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
