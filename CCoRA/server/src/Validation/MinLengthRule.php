<?php

namespace Cora\Validation;

class MinLengthRule extends AbstractRule {
    protected $minLength;

    public function __construct($minLength, $error) {
        parent::__construct($error);
        $this->minLength = $minLength;
    }
    
    public function validate($value): bool {
        return strlen($value) >= $this->getMinLength();
    }

    protected function getMinLength() {
        return $this->minLength;
    }
}
