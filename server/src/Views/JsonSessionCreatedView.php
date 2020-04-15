<?php

namespace Cora\Views;

class JsonSessionCreatedView implements SessionCreatedViewInterface {
    use JsonViewTrait;

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
