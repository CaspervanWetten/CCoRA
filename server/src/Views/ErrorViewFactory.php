<?php

namespace Cora\Views;

class ErrorViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonErrorView::class
        ];
    }
}
