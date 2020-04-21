<?php

namespace Cora\Domain\User\View;

use Cora\Views\AbstractViewFactory;

class UserCreatedViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonUserCreatedView::class
        ];
    }
}
