<?php

namespace Cora\Views;

class SvgImageView implements ImageViewInterface {
    public function getData(): string {
        return $this->data;
    }

    public function setData(string $data): void {
        $this->data = $data;
    }

    public function render(): string {
        return $this->getData();
    }

    public function getContentType(): string {
        return "image/svg+xml";
    }
}
