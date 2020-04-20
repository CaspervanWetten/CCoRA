<?php

namespace Cora\Views\Json;

use Cora\Views;

use Exception;

class JsonErrorView implements Views\ErrorViewInterface {
    protected $exception;

    public function getException(): Exception {
        return $this->exception;
    }

    public function setException(Exception $e): void {
        $this->exception = $e;
    }

    public function render(): string {
        return json_encode($this->getException()->getMessage());
    }
}
