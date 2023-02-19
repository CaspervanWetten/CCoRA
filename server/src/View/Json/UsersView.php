<?php

namespace Cora\View\Json;

use Cora\View\UsersViewInterface;

class UsersView implements UsersViewInterface {
    protected $users;

    public function getUsers(): array {
        return $this->users;
    }

    public function setUsers(array $users): void {
        $this->users = $users;
    }

    public function render(): string {
        return json_encode($this->getUsers());
    }
}
