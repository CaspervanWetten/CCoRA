<?php

namespace Cora\Validation;

abstract class AbstractRule implements RuleInterface {
    protected $errorMessage;

    public function __construct($errorMessage) {
        $this->errorMessage = $errorMessage;
    }

    public function getError(): string {
        return $this->errorMessage;
    }

    public abstract function validate($value): bool;
}
