<?php

namespace Cora\View\Json;

use Cora\View\UserCreatedViewInterface;

class UserCreatedView implements UserCreatedViewInterface {
    protected $userId;

    public function getUserId(): int {
        return $this->userId;
    }

    public function setUserId(int $id): void {
        $this->userId = $id;
    }

    public function render(): string {
        return json_encode([
            "user_id" => $this->getUserId()
        ]);
    }
}
