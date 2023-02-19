<?php

namespace Cora\View;

use Cora\Domain\Feedback\Feedback;

interface FeedbackViewInterface extends ViewInterface {
    public function getFeedback(): Feedback;
    public function setFeedback(Feedback $feedback): void;
}
