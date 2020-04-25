<?php

namespace Cora\Views\Json;

use Cora\Domain\Feedback\Feedback;
use Cora\Views;

class JsonFeedbackView implements Views\FeedbackViewInterface {
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
