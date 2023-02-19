<?php

namespace Cora\View\Svg;

use Cora\View\PetrinetImageViewInterface;

class PetrinetImageView implements PetrinetImageViewInterface {
    private $data;

    public function getData(): string {
        return $this->data;
    }

    public function setData(string $data): void {
        $this->data = $data;
    }

    public function render(): string {
        return $this->getData();
    }
}
