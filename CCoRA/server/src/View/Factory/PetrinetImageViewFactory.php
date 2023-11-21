<?php

namespace Cora\View\Factory;

use Cora\View\Svg;

class PetrinetImageViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "image/svg+xml" => Svg\PetrinetImageView::class
        ];
    }
}
