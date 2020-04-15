<?php

namespace Cora\Views;

class JsonCurrentSessionView implements CurrentSessionViewInterface {
    use JsonViewTrait;

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
