<?php

namespace Cora\Validation;

use Cora\Validation\RuleInterface as Rule;
use Traversable;

class RuleValidator implements ValidatorInterface {
    protected $rules;
    protected $errorMessage;

    public function __construct($rules = NULL) {
        $this->rules = array();
        $this->errorMessage = "";

        if (!is_null($rules)) {
            foreach($rules as $rule) {
                $this->addRule($rule);
            }
        }
    }
    
    public function validate($value): bool {
        foreach ($this->rules as $rule) 
            if (!$rule->validate($value)) {
                $this->errorMessage = $rule->getError();
                return false;
            }
        return true;
    }

    public function addRule(Rule $rule): void { 
        array_push($this->rules, $rule);
    }

    public function getError(): string {
        return $this->errorMessage;
    }
}
