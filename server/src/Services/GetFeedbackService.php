<?php

namespace Cora\Services;

use Cora\Converters\JsonToGraph;
use Cora\Domain\Petrinet\PetrinetRepository as PetriRepo;
use Cora\Domain\Session\SessionRepository as SessionRepo;
use Cora\Domain\Feedback\View\FeedbackViewInterface as View;
use Cora\SystemCheckers\CheckCoverabilityGraph;

use Cora\Domain\Session\InvalidSessionException;

class GetFeedbackService {
    public function get(
        View &$view,
        $jsonGraph,
        $userId,
        $sessionId,
        PetriRepo $petriRepo,
        SessionRepo $sessionRepo
    ) {
        $userId     = filter_var($userId, FILTER_SANITIZE_NUMBER_INT);
        $sessionId  = filter_var($sessionId, FILTER_SANITIZE_NUMBER_INT);
        if (!$sessionRepo->sessionExists($userId, $sessionId))
            throw new InvalidSessionException("The session id is invalid");
        $log        = $sessionRepo->getSessionLog($userId, $sessionId);
        $petrinetId = $log->getPetrinetId();
        $markingId  = $log->getMarkingId();
        $petrinet   = $petriRepo->getPetrinet($petrinetId);
        $converter  = new JsonToGraph($jsonGraph, $petrinet);
        $graph      = $converter->convert();
        $marking    = $petriRepo->getMarking($markingId, $petrinet);
        $checker    = new CheckCoverabilityGraph($graph, $petrinet, $marking);
        $feedback   = $checker->check();
        $sessionRepo->appendGraph($userId, $sessionId, $graph);
        $view->setFeedback($feedback);
    }
}
