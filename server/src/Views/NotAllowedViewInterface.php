<?php

namespace Cora\Views;

interface NotAllowedViewInterface extends ViewInterface {
    public function setUsedMethod($method);
    public function setAllowedMethods($methods);
}
