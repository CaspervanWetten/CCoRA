<?php

namespace Cora\Handlers\User;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractRequestHandler;
use Cora\Services\GetUserService;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\Exception\UserNotFoundException;
use Cora\Domain\User\View\UserViewFactory;
use Exception;

class GetUser extends AbstractRequestHandler {
    public function handle(Request $request, Response $response, $args) {
        $id = $args["id"];
        if (!isset($id))
            throw new Exception("No id given");
        try {
            $mediaType = $this->getMediaType($request);
            $view      = $this->getView($mediaType);
            $repo      = $this->container->get(UserRepo::class);
            $service   = $this->container->get(GetUserService::class);
            $service->getUser($view, $repo, $id);
            return $response->withHeader("Content-type", $mediaType)
                            ->write($view->render());
        } catch (UserNotFoundException $e) {
            return $this->fail($request, $response, $e, 404);
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new UserViewFactory();
    }
}
