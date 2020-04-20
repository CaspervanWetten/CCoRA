<?php

namespace Cora\Views\Json;

use Cora\Views;

class JsonCurrentSessionView implements Views\CurrentSessionViewInterface {
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
