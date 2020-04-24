<?php

namespace Cora\Services;

use Cora\Domain\Session\SessionRepository as SessionRepo;
use Cora\Repositories\PetrinetRepository as PetriRepo;
use Cora\Repositories\PetrinetNotFoundException;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\UserNotFoundException;
use Cora\Views\SessionCreatedViewInterface as View;

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
            throw new UserNotFoundException(
                "Could not start session: user does not exist");
        if (!$petriRepo->petrinetExists($pid))
            throw new PetrinetNotFoundException(
                "Could not start session: Petri net does not exist");
        $session = $sessionRepo->createNewSession($uid, $pid);
        $view->setSessionId($session->getId());
    }
}
