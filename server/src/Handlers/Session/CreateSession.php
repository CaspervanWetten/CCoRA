<?php

namespace Cora\Handlers\Session;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\UserRepository as UserRepo;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Repositories\SessionRepository as SessionRepo;

use Exception;

class CreateSession extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $id = filter_var($args["id"], FILTER_SANITIZE_NUMBER_INT);
        $pid = filter_var($args["pid"], FILTER_SANITIZE_NUMBER_INT);
        $userRepo = $this->container->get(UserRepo::class);
        if (!$userRepo->userExists("id", $id)) 
            throw new Exception("Could not start session: user does not exist");
        $petriRepo = $this->container->get(PetrinetRepo::class);
        if (!$petriRepo->petrinetExists($pid))
            throw new Exception("Could not start session: Petri net does not "
                                . "exist");
        $sessionRepo = $this->container->get(SessionRepo::class);
        $session = $sessionRepo->createNewSession($id, $pid);
        if ($session === FALSE)
            throw new Exception("Could not start session: logging error");
        return $response->withJson(["session_id" => $session]);
    }
}
