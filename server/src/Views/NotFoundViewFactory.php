<?php

namespace Cora\Views;

class NotFoundViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonNotFoundView::class
        ];
    }
}
