<?php

namespace Cora\View;

interface PetrinetImageViewInterface extends ViewInterface {
    public function getData(): string;
    public function setData(string $data): void;
}
