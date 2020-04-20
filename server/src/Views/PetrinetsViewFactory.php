<?php

namespace Cora\Views;

class PetrinetsViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonPetrinetsView::class
        ];
    }
}
