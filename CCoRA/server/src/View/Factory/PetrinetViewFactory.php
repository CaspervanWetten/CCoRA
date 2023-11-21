<?php

namespace Cora\View\Factory;

use Cora\View\Json;

class PetrinetViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\PetrinetView::class
        ];
    }
}
