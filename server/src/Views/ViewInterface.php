<?php

namespace Cora\Views;

interface ViewInterface {
    public function getContentType(): string;
    public function toString(): string;
}
