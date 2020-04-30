<?php

namespace Cora\Domain\Petrinet\View;

use Cora\Views\AbstractViewFactory;

use Cora\Views\SvgImageView;

class PetrinetImageViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "image/svg+xml" => SvgImageView::class
        ];
    }
}
