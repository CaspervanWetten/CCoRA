<?php

namespace Cora\Handlers\User;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\UserRegistrationException;
use Cora\Handlers\AbstractHandler;
use Cora\Services\RegisterUserService;
use Cora\Views\AbstractViewFactory;
use Cora\Views\UserCreatedViewFactory;
use Exception;

class RegisterUser extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $body = $request->getParsedBody();
        if (!isset($body["name"]))
            throw new Exception("No name supplied");
        try {
            $mediaType = $this->getMediaType($request);
            $repo      = $this->container->get(UserRepo::class);
            $view      = $this->getView($mediaType);
            $service   = $this->container->get(RegisterUserService::class);
            $service->register($view, $repo, $body["name"]);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(201)
                            ->write($view->render());
        } catch (UserRegistrationException $e) {
            $mediaType = $this->getErrorMediaType($request);
            $view = $this->getErrorView($mediaType);
            $view->setException($e);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(400)
                            ->write($view->render());
        }
    }

    protected function getViewFactory(): AbstractViewFactory {
        return new UserCreatedViewFactory();
    }
}
