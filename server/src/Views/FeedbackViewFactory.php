<?php

namespace Cora\Views;

class FeedbackViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => Json\JsonFeedbackView::class
        ];
    }
}
