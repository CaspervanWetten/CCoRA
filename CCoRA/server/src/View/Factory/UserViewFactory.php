<?php

namespace Cora\View\Factory;

use Cora\View\Json;

class UserViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\UserView::class
        ];
    }
}
