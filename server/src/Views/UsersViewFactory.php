<?php

namespace Cora\Views;

class UsersViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonUsersView::class
        ];
    }
}
