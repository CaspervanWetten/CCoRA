<?php

namespace Cora\Views;

class NotAllowedViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonNotAllowedView::class
        ];
    }
}
