<?php

namespace Cora\View\Factory;

use Cora\View\Json;

class PetrinetCreatedViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\PetrinetCreatedView::class
        ];
    }
}
