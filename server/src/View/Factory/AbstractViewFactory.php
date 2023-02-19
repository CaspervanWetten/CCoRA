<?php

namespace Cora\View\Factory;

use Exception;

abstract class AbstractViewFactory implements ViewFactory {
    public function create(string $mediaType) {
        $mediaType = strtolower(trim($mediaType));
        if (in_array($mediaType, $this->getContentTypes())) {
            $cons = $this->getMediaAssociations()[$mediaType];
            return new $cons();
        }
        throw new Exception("Could not create view: unsupported media type");
    }

    public function getContentTypes(): array {
        return array_keys($this->getMediaAssociations());
    }

    protected abstract function getMediaAssociations(): array;
}
