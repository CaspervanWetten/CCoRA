<?php

namespace Cora\Domain\Session\View;

class JsonSessionCreatedView implements SessionCreatedViewInterface {
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
