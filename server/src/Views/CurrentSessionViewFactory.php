<?php

namespace Cora\Views;

class CurrentSessionViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonCurrentSessionView::class
        ];
    }
}
