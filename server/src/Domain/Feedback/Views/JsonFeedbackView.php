<?php

namespace Cora\Domain\Feedback\Views;

use Cora\Domain\Feedback\Feedback;

class JsonFeedbackView implements FeedbackViewInterface {
    protected $feedback;

    public function getFeedback(): Feedback {
        return $this->feedback;
    }

    public function setFeedback(Feedback $feedback): void {
        $this->feedback = $feedback;
    }

    public function render(): string {
        return json_encode($this->getFeedback());
    }
}
