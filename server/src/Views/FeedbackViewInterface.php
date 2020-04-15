<?php

namespace Cora\Views;

use Cora\Feedback\Feedback;

interface FeedbackViewInterface extends ViewInterface {
    public function getFeedback(): Feedback;
    public function setFeedback(Feedback $feedback): void;
}
