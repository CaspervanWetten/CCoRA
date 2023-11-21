<?php

namespace Cora\View\Factory;

use Cora\View\Json;

class SessionCreatedViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\SessionCreatedView::class
        ];
    }
}
