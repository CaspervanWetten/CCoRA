<?php

namespace Cora\Validation;


class RegexRule implements RuleInterface {
    protected $regex;

    public function __construct($regex, $error) {
        parent::__construct($error);
        $this->regex = $regex;
    }

    public function validate($value): bool {
        return preg_match($this->regex, $value) === 1;
    }
}
