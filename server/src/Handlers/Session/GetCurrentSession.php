<?php

namespace Cora\Handlers\Session;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Repositories\SessionRepository as SessionRepo;

use Exception;

class GetCurrentSession extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        if (!isset($args["id"]))
            throw new Exception("No id supplied");
        $id = filter_var($args["id"], FILTER_SANITIZE_NUMBER_INT);
        $userRepo = $this->container->get(UserRepo::class);
        if (!$userRepo->userExists("id", $id))
            throw new Exception("This user does not exist");
        $sessionRepo = $this->container->get(SessionRepo::class);
        $session = $sessionRepo->getCurrentSession($id);
        if ($session === FALSE)
            throw new Exception("A session for this user has not yet been created");
        return $response->withJson([
            "session_id" => $session]);
    }
}
