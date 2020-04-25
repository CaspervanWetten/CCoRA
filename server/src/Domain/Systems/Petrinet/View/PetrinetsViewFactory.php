<?php

namespace Cora\Domain\Systems\Petrinet\View;

use Cora\Views\AbstractViewFactory;

class PetrinetsViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonPetrinetsView::class
        ];
    }
}
