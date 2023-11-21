<?php

namespace Cora\Service;

use Cora\Converter\JsonToGraph;
use Cora\Repository\PetrinetRepository;
use Cora\Repository\SessionRepository;
use Cora\SystemChecker\CheckCoverabilityGraph;

use Cora\Exception\InvalidSessionException;

class GetFeedbackService {
    private $petrinetRepository, $sessionRepository;

    public function __construct(PetrinetRepository $pr, SessionRepository $sr) {
        $this->petrinetRepository = $pr;
        $this->sessionRepository = $sr;
    }

    public function get($jsonGraph, $userId, $sessionId) {
        $userId    = intval(filter_var($userId, FILTER_SANITIZE_NUMBER_INT));
        $sessionId = intval(filter_var($sessionId, FILTER_SANITIZE_NUMBER_INT));

        if (!$this->sessionRepository->sessionExists($userId, $sessionId))
            throw new InvalidSessionException("The session id is invalid");

        $log        = $this->sessionRepository->getSessionLog($userId, $sessionId);
        $petrinetId = $log->getPetrinetId();
        $markingId  = $log->getMarkingId();
        $petrinet   = $this->petrinetRepository->getPetrinet($petrinetId);
        $converter  = new JsonToGraph($jsonGraph, $petrinet);
        $graph      = $converter->convert();
        $marking    = $this->petrinetRepository->getMarking($markingId, $petrinet);
        $checker    = new CheckCoverabilityGraph($graph, $petrinet, $marking);
        $feedback   = $checker->check();
        $this->sessionRepository->appendGraph($userId, $sessionId, $graph);
        return $feedback;
    }
}
