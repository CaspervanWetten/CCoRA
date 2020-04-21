<?php

namespace Cora\Domain\User\View;

use Cora\Views\ViewInterface;

interface UsersViewInterface extends ViewInterface {
    public function getUsers(): array;
    public function setUsers(array $users): void;
}
