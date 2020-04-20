<?php

namespace Cora\Views;

use Exception;

interface ErrorViewInterface extends ViewInterface {
    public function getException(): Exception;
    public function setException(Exception $e): void;
}
