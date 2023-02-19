<?php

namespace Cora\View;

use Cora\Domain\User\User;

interface UserViewInterface {
    public function getUser(): User;
    public function setUser(User $user): void;
}
