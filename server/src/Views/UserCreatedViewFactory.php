<?php

namespace Cora\Views;

class UserCreatedViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonUserCreatedView::class
        ];
    }
}
