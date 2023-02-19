<?php

namespace Cora\View\Factory;

use Cora\View\Json;

class UserCreatedViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\UserCreatedView::class
        ];
    }
}
