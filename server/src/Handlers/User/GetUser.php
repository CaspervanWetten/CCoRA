<?php

namespace Cora\Handlers\User;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Services\GetUserService;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Views\UserViewFactory;
use Exception;

class GetUser extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $id = $args["id"];
        if (!isset($id))
            throw new Exception("No id given");
        $mediaType = $this->getMediaType($request);
        $view      = $this->getView($mediaType);
        $repo      = $this->container->get(UserRepo::class);
        $service   = $this->container->get(GetUserService::class);
        $service->getUser($view, $repo, $id);
        return $response->withHeader("Content-type", $mediaType)
                        ->write($view->render());
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new UserViewFactory();
    }
}
