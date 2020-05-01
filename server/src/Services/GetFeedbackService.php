<?php

namespace Cora\Services;

use Cora\Converters\JsonToGraph;
use Cora\Domain\Petrinet\PetrinetRepository as PetriRepo;
use Cora\Domain\Session\SessionRepository as SessionRepo;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\Feedback\View\FeedbackViewInterface as View;
use Cora\SystemCheckers\CheckCoverabilityGraph;

use Exception;

class GetFeedbackService {
    public function get(
        View &$view,
        $jsonGraph,
        $userId,
        $petrinetId,
        $sessionId,
        $markingId,
        UserRepo $userRepo,
        PetriRepo $petriRepo,
        SessionRepo $sessionRepo)
    {
        if (is_null($userId))
            throw new Exception("No user specified");
        if (is_null($petrinetId))
            throw new Exception("No Petri net specified");
        if (is_null($sessionId))
            throw new Exception("No session specified");
        if (is_null($markingId))
            throw new Exception("No initial marking specified");
        $userId     = filter_var($userId, FILTER_SANITIZE_NUMBER_INT);
        $petrinetId = filter_var($petrinetId, FILTER_SANITIZE_NUMBER_INT);
        $sessionId  = filter_var($sessionId, FILTER_SANITIZE_NUMBER_INT);
        $markingId  = filter_var($markingId, FILTER_SANITIZE_NUMBER_INT);

        if (!$userRepo->userExists("id", $userId))
            throw new Exception("User does not exist");
        if (!$petriRepo->petrinetExists($petrinetId))
            throw new Exception("Petri net does not exist");
        if (!$petriRepo->markingExists($markingId))
            throw new Exception("This marking does not exist");
        $petrinet = $petriRepo->getPetrinet($petrinetId);
        $converter = new JsonToGraph($jsonGraph, $petrinet);
        $graph = $converter->convert();
        $marking = $petriRepo->getMarking($markingId, $petrinet);
        $checker = new CheckCoverabilityGraph($graph, $petrinet, $marking);
        $feedback = $checker->check();
        $sessionRepo->appendGraph($userId, $sessionId, $petrinetId, $graph);
        $view->setFeedback($feedback);
    }
}
