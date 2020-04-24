<?php

namespace Cora\Domain\Session;

use JsonSerializable;

class Session implements JsonSerializable {
    protected $sessionId;

    public function __construct(int $id) {
        $this->sessionId = $id;
    }

    public function getId(): int {
        return $this->sessionId;
    }

    public function jsonSerialize() {
        return [
            "session_id" => $this->getId()
        ];
    }
}
