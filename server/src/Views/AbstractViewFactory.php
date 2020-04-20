<?php

namespace Cora\Views;

use Exception;

abstract class AbstractViewFactory {
    public function create(string $mediaType) {
        $mediaType = strtolower(trim($mediaType));
        if (in_array($mediaType, $this->getMediaTypes())) {
            $cons = $this->getMediaAssociations()[$mediaType];
            return new $cons();
        }
        throw new Exception("Could not create view: unsupported media type");
    }

    public function getMediaTypes(): array {
        return array_keys($this->getMediaAssociations());
    }

    protected abstract function getMediaAssociations(): array;
}
