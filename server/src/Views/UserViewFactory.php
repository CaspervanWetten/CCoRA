<?php

namespace Cora\Views;

class UserViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonUserView::class
        ];
    }
}
