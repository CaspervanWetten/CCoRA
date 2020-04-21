<?php

namespace Cora\Domain\User\View\Json;

use Cora\Domain\User\User;
use Cora\Domain\User\View\UserViewInterface as IUserView;

class JsonUserView implements IUserView {
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
