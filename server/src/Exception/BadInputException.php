<?php

namespace Cora\Exception;

use Throwable;
use Exception;

class BadInputException extends Exception {
    public function __construct(string $message, ?Throwable $previous = null) {
        parent::__construct($message, 400, $previous);
    }
}
