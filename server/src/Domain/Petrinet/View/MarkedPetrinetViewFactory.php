<?php

namespace Cora\Domain\Petrinet\View;

use Cora\Views\AbstractViewFactory;

class MarkedPetrinetViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonMarkedPetrinetView::class
        ];
    }
}
