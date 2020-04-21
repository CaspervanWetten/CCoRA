<?php

namespace Cora\Domain\User\View\Json;

use Cora\Domain\User\View\UsersViewInterface;

class JsonUsersView implements UsersViewInterface {
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
