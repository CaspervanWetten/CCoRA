<?php

namespace Cora\Views\Json;

use Cora\Views;

class JsonSessionCreatedView implements Views\SessionCreatedViewInterface {
    protected $sessionId;

    public function getSessionId(): int {
        return $this->sessionId;
    }

    public function setSessionId(int $id): void {
        $this->sessionId = $id;
    }

    public function render(): string {
        return json_encode([
            "session_id" => $this->getSessionId()
        ]);
    }
}
