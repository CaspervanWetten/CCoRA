<?php

namespace Cora\Views;

trait JsonViewTrait {
    public function getContentType(): string {
        return "application/json";
    }
}
