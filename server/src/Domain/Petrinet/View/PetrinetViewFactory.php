<?php

namespace Cora\Domain\Petrinet\View;

use Cora\Views\AbstractViewFactory;

class PetrinetViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonPetrinetView::class
        ];
    }
}
