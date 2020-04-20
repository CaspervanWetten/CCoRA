<?php

namespace Cora\Views;

class SessionCreatedViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonSessionCreatedView::class
        ];
    }
}
