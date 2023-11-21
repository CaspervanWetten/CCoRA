<?php

namespace Cora\Validation;

class MaxLengthRule extends AbstractRule {
    protected $maxLength;

    public function __construct($maxLength, $error) {
        parent::__construct($error);
        $this->maxLength = $maxLength;
    }

    public function validate($value): bool {
        return strlen($value) <= $this->getMaxLength();
    }

    public function getMaxLength() {
        return $this->maxLength;
    }
}
