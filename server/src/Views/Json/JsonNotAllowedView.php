<?php

namespace Cora\Views\Json;

use Cora\Views\NotAllowedViewInterface;

class JsonNotAllowedView implements NotAllowedViewInterface {
    protected $usedMethod;
    protected $methods;

    public function setUsedMethod($method) {
        $this->usedMethod = $method;
    }

    public function setAllowedMethods($methods) {
        $this->methods = $methods;
    }

    protected function getUsedMethod() {
        return $this->usedMethod;
    }

    protected function getAllowedMethods() {
        return $this->methods;
    }

    public function render(): string {
        return json_encode([
            "error" => sprintf("Method %s is not allowed for this request",
                               $this->getUsedMethod()),
            "allowed_methods" => $this->getAllowedMethods()
        ]);
    }
}
