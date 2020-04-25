<?php

namespace Cora\Domain\Systems\Petrinet\View;

use Cora\Views\AbstractViewFactory;

class PetrinetCreatedViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonPetrinetCreatedView::class
        ];
    }
}
