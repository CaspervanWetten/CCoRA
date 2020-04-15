<?php

namespace Cora\Views;

interface ImageViewInterface extends ViewInterface {
    public function getData(): string;
    public function setData(string $data): void;
}
