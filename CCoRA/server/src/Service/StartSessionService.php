<?php

namespace Cora\Service;

use Cora\Repository\SessionRepository as SessionRepo;
use Cora\Repository\PetrinetRepository as PetriRepo;
use Cora\Repository\UserRepository as UserRepo;

use Cora\Exception\UserNotFoundException;
use Cora\Exception\PetrinetNotFoundException;
use Cora\Exception\MarkingNotFoundException;

class StartSessionService {
    private $userRepository, $petrinetRepository, $sessionRepository;

    public function __construct(SessionRepo $sr, PetriRepo $pr, UserRepo $ur) {
        $this->userRepository = $ur;
        $this->petrinetRepository = $pr;
        $this->sessionRepository = $sr;
    }

    public function start($uid, $pid, $mid) {
        $uid = filter_var($uid, FILTER_SANITIZE_NUMBER_INT);
        $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
        $mid = filter_var($mid, FILTER_SANITIZE_NUMBER_INT);

        if (!$this->userRepository->userExists("id", $uid))
            throw new UserNotFoundException(
                "Could not start session: user does not exist");
        if (!$this->petrinetRepository->petrinetExists($pid))
            throw new PetrinetNotFoundException(
                "Could not start session: Petri net does not exist");
        if (!$this->petrinetRepository->markingExists($mid))
            throw new MarkingNotFoundException(
                "Could not start session: Marking does not exist");

        return $this->sessionRepository->createNewSession($uid, $pid, $mid);
    }
}
