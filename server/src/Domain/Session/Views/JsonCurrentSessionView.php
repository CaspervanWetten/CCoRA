<?php

namespace Cora\Domain\Session\Views;

class JsonCurrentSessionView implements CurrentSessionViewInterface {
    protected $id;

    public function getSessionId(): int {
        return $this->id;
    }

    public function setSessionId(int $id): void {
        $this->id = $id;
    }

    public function render(): string {
        return json_encode([
            "session_id" => $this->getSessionId()
        ]);
    }
}
