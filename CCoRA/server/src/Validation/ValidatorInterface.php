<?php

namespace Cora\Validation;

use Cora\Validation\RuleInterface as Rule;

interface ValidatorInterface {
    public function addRule(Rule $rule): void;
    public function validate($value): bool;
    public function getError(): string;
}
