<?php

namespace Cora\Validation;

interface RuleInterface {
    public function validate($value): bool;
    public function getError(): string;
}
