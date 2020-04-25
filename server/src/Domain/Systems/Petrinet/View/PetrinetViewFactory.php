<?php

namespace Cora\Domain\Systems\Petrinet\View;

use Cora\Views\AbstractViewFactory;

class PetrinetViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonPetrinetView::class
        ];
    }
}
