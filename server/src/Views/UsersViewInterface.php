<?php

namespace Cora\Views;

interface UsersViewInterface extends ViewInterface {
    public function getUsers(): array;
    public function setUsers(array $users): void;
}
