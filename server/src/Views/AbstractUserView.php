<?php

namespace Cora\Views;

use Cora\User\User;

abstract class AbstractUserView implements ViewInterface {
    protected $user;

    public function getUser(): User {
        return $this->user;
    }
    
    public function setUser(User $user): void {
        $this->user = $user;
    }
}
