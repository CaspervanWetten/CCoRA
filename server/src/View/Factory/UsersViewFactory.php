<?php

namespace Cora\View\Factory;

use Cora\View\Json;

class UsersViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\UsersView::class
        ];
    }
}
