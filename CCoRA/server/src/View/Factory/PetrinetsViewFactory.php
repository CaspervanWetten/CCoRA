<?php

namespace Cora\View\Factory;

use Cora\View\Json;

class PetrinetsViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\PetrinetsView::class
        ];
    }
}
