<?php

namespace Cora\Domain\Feedback\View;

use Cora\Domain\Feedback\Feedback;
use Cora\Views\ViewInterface;

interface FeedbackViewInterface extends ViewInterface {
    public function getFeedback(): Feedback;
    public function setFeedback(Feedback $feedback): void;
}
