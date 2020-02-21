<?php

namespace Cora\Views;

use Exception;

class JsonErrorView implements ViewInterface {
    use JsonViewTrait;

    protected $exception;
    
    public function __construct(Exception $e) {
        $this->exception = $e;
    }

    public function toString(): string {
        return json_encode($this->getException()->getMessage());
    }

    protected function getException(): Exception {
        return $this->exception;
    }
}
