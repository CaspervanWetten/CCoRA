<?php

namespace Cora\View;

use Cora\View\ViewInterface;

interface UsersViewInterface extends ViewInterface {
    public function getUsers(): array;
    public function setUsers(array $users): void;
}
