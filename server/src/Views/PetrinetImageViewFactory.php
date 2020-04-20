<?php

namespace Cora\Views;

class PetrinetImageViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "image/svg+xml" => SvgImageView::class
        ];
    }
}
