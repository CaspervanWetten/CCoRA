<?php

namespace Cora\View\Factory;

use Cora\View\Json;

class CurrentSessionViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\CurrentSessionView::class
        ];
    }
}
