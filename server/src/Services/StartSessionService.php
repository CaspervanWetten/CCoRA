<?php

namespace Cora\Services;

use Cora\Repositories\SessionRepository as SessionRepo;
use Cora\Repositories\PetrinetRepository as PetriRepo;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Views\SessionCreatedViewInterface as View;

use Exception;

class StartSessionService {
    public function start(
        View &$view,
        $uid,
        $pid,
        SessionRepo $sessionRepo,
        UserRepo $userRepo,
        PetriRepo $petriRepo)
    {
        $uid = filter_var($uid, FILTER_SANITIZE_NUMBER_INT);
        $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
        if (!$userRepo->userExists("id", $uid))
            throw new Exception("Could not start session: user does not exist");
        if (!$petriRepo->petrinetExists($pid))
            throw new Exception("Could not start session: Petri net does not exist");
        $session = $sessionRepo->createNewSession($uid, $pid);
        if ($session === FALSE)
            throw new Exception("Could not start session: logging error");
        $view->setSessionId($session);
    }
}
