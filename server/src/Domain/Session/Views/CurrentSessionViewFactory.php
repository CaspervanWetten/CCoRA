<?php

namespace Cora\Domain\Session\Views;

use Cora\Views\AbstractViewFactory;

class CurrentSessionViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => JsonCurrentSessionView::class
        ];
    }
}
