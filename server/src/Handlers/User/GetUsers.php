<?php

namespace Cora\Handlers\User;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\View\UsersViewFactory;
use Cora\Handlers\AbstractHandler;
use Cora\Services\GetUsersService;

class GetUsers extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $mediaType = $this->getMediaType($request);
        $repo      = $this->container->get(UserRepo::class);
        $view      = $this->getView($mediaType);
        $service   = $this->container->get(GetUsersService::class);
        $service->getUsers($view, $repo, $args["page"], $args["limit"]);
        return $response->withHeader("Content-type", $mediaType)
                        ->withStatus(200)
                        ->write($view->render());
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new UsersViewFactory();
    }
}
