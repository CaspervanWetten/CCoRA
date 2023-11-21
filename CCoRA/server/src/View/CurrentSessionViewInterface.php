<?php

namespace Cora\View;

use Cora\Domain\Session\Session;

interface CurrentSessionViewInterface extends ViewInterface {
    public function getSession(): Session;
    public function setSession(Session $session);
}
