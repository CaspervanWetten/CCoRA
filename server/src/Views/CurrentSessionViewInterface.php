<?php

namespace Cora\Views;

interface CurrentSessionViewInterface extends ViewInterface {
    public function getSessionId(): int;
    public function setSessionId(int $id): void;
}
