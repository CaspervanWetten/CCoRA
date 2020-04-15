<?php

namespace Cora\Views;

class JsonUserCreatedView implements UserCreatedViewInterface {
    use JsonViewTrait;

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
