<?php

namespace Cora\Views\Json;

use Cora\Views;
use Cora\Domain\User\User;

class JsonUserView implements Views\UserViewInterface {
    protected $user;

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }

    public function render(): string {
        return json_encode($this->getUser());
    }
}
