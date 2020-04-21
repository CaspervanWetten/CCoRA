<?php

namespace Cora\Domain\User\View;

use Cora\Views\AbstractViewFactory;

class UserViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonUserView::class
        ];
    }
}
