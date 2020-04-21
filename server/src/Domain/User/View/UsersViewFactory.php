<?php

namespace Cora\Domain\User\View;

use Cora\Views\AbstractViewFactory;

class UsersViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonUsersView::class
        ];
    }
}
