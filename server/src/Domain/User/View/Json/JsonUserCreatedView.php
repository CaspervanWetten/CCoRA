<?php

namespace Cora\Domain\User\View\Json;

use Cora\Domain\User\View\UserCreatedViewInterface;

class JsonUserCreatedView implements UserCreatedViewInterface {
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
