<?php

namespace Cora\Domain\Session;

use JsonSerializable;

class MetaSessionLog implements JsonSerializable {
    protected $userId;
    protected $sessionCount;

    public function __construct(int $user, int $count=0) {
        $this->userId = $user;
        $this->sessionCount = $count;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getSessionCount() {
        return $this->sessionCount;
    }

    public function incrementSessionCounter() {
        $this->sessionCount++;
    }

    public function jsonSerialize(): mixed {
        return [
            "user_id" => $this->getUserId(),
            "session_counter" => $this->getSessionCount()
        ];
    }
}
