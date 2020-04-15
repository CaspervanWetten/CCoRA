<?php

namespace Cora\Views;

interface UserCreatedViewInterface extends ViewInterface {
    public function getUserId(): int;
    public function setUserId(int $id): void;
}
