<?php

namespace Cora\Handlers\User;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\UserRepository as UserRepo;

class GetUser extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        if (!isset($args["id"]))
            throw new \Exception("No id given");
        $id = filter_var($args["id"], FILTER_SANITIZE_NUMBER_INT);
        $repo = $this->container->get(UserRepo::class);
        $user = $repo->getUser('id', $id);
        if (empty($user))
            return $response->withStatus(404);
        return $response->withJson($user);
    }
}
