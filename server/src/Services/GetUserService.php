<?php

namespace Cora\Services;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\User\View\UserViewInterface as UserView;
use Cora\Domain\User\Exception\UserNotFoundException;

class GetUserService {
    public function getUser(UserView $view, UserRepo $repo, $id) {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $user = $repo->getUser('id', $id);
        if (is_null($user))
            throw new UserNotFoundException("A user with this id does not exist");
        $view->setUser($user);
        return $view;
    }
}
