<?php

namespace Cora\Domain\Session\View;

use Cora\Views\AbstractViewFactory;

class SessionCreatedViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => JsonSessionCreatedView::class
        ];
    }
}
