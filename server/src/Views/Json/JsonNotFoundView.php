<?php

namespace Cora\Views\Json;

use Cora\Views\ViewInterface;

class JsonNotFoundView implements ViewInterface {
    public function render(): string {
        return json_encode("The requested resource could not be found");
    }
}
