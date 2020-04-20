<?php

namespace Cora\Views;

class PetrinetViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonPetrinetView::class
        ];
    }
}
