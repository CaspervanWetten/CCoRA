<?php

namespace Cora\Domain\Petrinet\View;

use Cora\Views\AbstractViewFactory;

class PetrinetsViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonPetrinetsView::class
        ];
    }
}
