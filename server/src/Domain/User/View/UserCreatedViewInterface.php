<?php

namespace Cora\Domain\User\View;

use Cora\Views\ViewInterface;

interface UserCreatedViewInterface extends ViewInterface {
    public function getUserId(): int;
    public function setUserId(int $id): void;
}
