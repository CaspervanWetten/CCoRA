<?php

namespace Cora\Services;

use Cora\Converters\JsonToGraph;
use Cora\Domain\Petrinet\PetrinetRepository as PetriRepo;
use Cora\Domain\Session\SessionRepository as SessionRepo;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\Feedback\View\FeedbackViewInterface as View;
use Cora\SystemCheckers\CheckCoverabilityGraph;

use Cora\Domain\Feedback\NoUserException;
use Cora\Domain\Feedback\NoPetrinetException;
use Cora\Domain\Feedback\NoSessionException;
use Cora\Domain\Feedback\NoInitialMarkingException;
use Cora\Domain\User\Exception\UserNotFoundException;
use Cora\Domain\Petrinet\PetrinetNotFoundException;
use Cora\Domain\Petrinet\Marking\MarkingNotFoundException;
use Cora\Domain\Session\InvalidSessionException;

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
        SessionRepo $sessionRepo
    ) {
        if (is_null($userId))
            throw new NoUserException("No user specified");
        if (is_null($petrinetId))
            throw new NoPetrinetException("No Petri net specified");
        if (is_null($sessionId))
            throw new NoSessionException("No session specified");
        if (is_null($markingId))
            throw new NoInitialMarkingException("No initial marking specified");
        $userId     = filter_var($userId, FILTER_SANITIZE_NUMBER_INT);
        $petrinetId = filter_var($petrinetId, FILTER_SANITIZE_NUMBER_INT);
        $sessionId  = filter_var($sessionId, FILTER_SANITIZE_NUMBER_INT);
        $markingId  = filter_var($markingId, FILTER_SANITIZE_NUMBER_INT);

        if (!$userRepo->userExists("id", $userId))
            throw new UserNotFoundException("User does not exist");
        if (!$petriRepo->petrinetExists($petrinetId))
            throw new PetrinetNotFoundException("Petri net does not exist");
        if (!$petriRepo->markingExists($markingId))
            throw new MarkingNotFoundException("This marking does not exist");
        if (!$sessionRepo->sessionExists($sessionId, $userId, $petrinetId))
            throw new InvalidSessionException("The session id is invalid");
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
