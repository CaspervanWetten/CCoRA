<?php

namespace Cora\Domain\Feedback\View;

use Cora\Views\AbstractViewFactory;

class FeedbackViewFactory extends AbstractViewFactory {
    protected function getMediaAssociations(): array {
        return [
            "application/json" => JsonFeedbackView::class
        ];
    }
}
