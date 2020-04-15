<?php

namespace Cora\Views;

use Cora\Feedback\Feedback;

class JsonFeedbackView implements FeedbackViewInterface {
    use JsonViewTrait;

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
