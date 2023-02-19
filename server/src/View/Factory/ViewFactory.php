<?php

namespace Cora\View\Factory;

interface ViewFactory {
    public function create(string $mediaType);
    public function getContentTypes();
}
