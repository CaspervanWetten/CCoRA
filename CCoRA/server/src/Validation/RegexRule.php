<?php

namespace Cora\Validation;


class RegexRule extends AbstractRule {
    protected $regex;

    public function __construct($regex, $error) {
        parent::__construct($error);
        $this->regex = $regex;
    }

    public function validate($value): bool {
        $result = preg_match($this->regex, $value);
        return $result === 1;
    }
}
