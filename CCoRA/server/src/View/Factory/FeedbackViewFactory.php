<?php

namespace Cora\View\Factory;

use Cora\View\Json;

class FeedbackViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\FeedbackView::class
        ];
    }
}
