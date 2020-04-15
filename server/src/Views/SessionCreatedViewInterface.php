<?php

namespace Cora\Views;

interface SessionCreatedViewInterface extends ViewInterface {
    public function getSessionId(): int;
    public function setSessionId(int $id): void;
}
