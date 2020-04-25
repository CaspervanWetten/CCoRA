<?php

namespace Cora\Domain\Session\View;

use Cora\Views\ViewInterface;

interface SessionCreatedViewInterface extends ViewInterface {
    public function getSessionId(): int;
    public function setSessionId(int $id): void;
}
