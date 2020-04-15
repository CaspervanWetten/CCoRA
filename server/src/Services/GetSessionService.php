<?php

namespace Cora\Services;

use Cora\Repositories\SessionRepository as SessionRepo;
use Cora\Domain\User\UserRepository as UserRepo;

use Exception;

class GetSessionService {
    public function get($uid, SessionRepo $sessionRepo, UserRepo $userRepo) {
        $id = filter_var($uid, FILTER_SANITIZE_NUMBER_INT);
        if (!$userRepo->userExists("id", $uid))
            throw new Exception("This user does not exist");
        $session = $sessionRepo->getCurrentSession($id);
        if ($session === FALSE)
            throw new Exception("A sessin for this user has not yet been created");
        return $session;
    }
}
