<?php

namespace Cora\View\Json;


use Cora\View\CurrentSessionViewInterface;
use Cora\Domain\Session\Session;

class CurrentSessionView implements CurrentSessionViewInterface {
    protected $session;

    public function getSession(): Session {
        return $this->session;
    }

    public function setSession(Session $session): void {
        $this->session = $session;
    }

    public function render(): string {
        return json_encode([
            "session_id" => $this->getSession()->getId()
        ]);
    }
}
