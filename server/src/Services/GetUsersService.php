<?php

namespace Cora\Services;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Views\UsersViewInterface as View;

use Cora\Utils\Paginator;

class GetUsersService {
    public function getUsers(View &$view, UserRepo $repo, $page, $limit) {
        $limit = min(MAX_USER_RESULT_SIZE,
                     filter_var($limit, FILTER_SANITIZE_NUMBER_INT));
        $page = filter_var($page, FILTER_SANITIZE_NUMBER_INT);
        $paginator = new Paginator($limit, $page);
        $users = $repo->getUsers(NULL, $paginator->limit(), $paginator->offset());
        $view->setUsers($users);
    }
}
