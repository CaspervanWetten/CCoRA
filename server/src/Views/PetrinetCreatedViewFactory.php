<?php

namespace Cora\Views;

class PetrinetCreatedViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonPetrinetCreatedView::class
        ];
    }
}
