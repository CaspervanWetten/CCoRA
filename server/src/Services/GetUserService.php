<?php

namespace Cora\Services;

use Cora\User\User;
use Cora\User\UserNotFoundException;
use Cora\User\UserRepository as UserRepo;
use Cora\Views\AbstractUserView as UserView;

use DateTime;

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
