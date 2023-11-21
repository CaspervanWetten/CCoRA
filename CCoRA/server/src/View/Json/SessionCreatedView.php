<?php

namespace Cora\View\Json;

use Cora\View\SessionCreatedViewInterface;
use Cora\Domain\Session\Session;

class SessionCreatedView implements SessionCreatedViewInterface {
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
