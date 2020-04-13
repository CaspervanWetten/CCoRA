<?php

namespace Cora\Handlers\Feedback;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Domain\Systems\Petrinet\MarkedPetrinet;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Repositories\SessionRepository as SessionRepo;
use Cora\Converters\JsonToGraph2;
use Cora\SystemCheckers\CheckCoverabilityGraph;

use Exception;

class CoverabilityFeedback extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        if (!isset($args["user_id"]))
            throw new Exception("No user id supplied");
        if (!isset($args["petrinet_id"]))
            throw new Exception("No Petri net id supplied");
        if (!isset($args["session_id"]))
            throw new Exception("No session id supplied"); 

        $user = filter_var($args["user_id"], FILTER_SANITIZE_NUMBER_INT);
        $pid  = filter_var($args["petrinet_id"], FILTER_SANITIZE_NUMBER_INT);
        $sid  = filter_var($args["session_id"], FILTER_SANITIZE_NUMBER_INT);

        $userRepo = $this->container->get(UserRepo::class);
        if (!$userRepo->userExists("id", $user))
            throw new Exception("User does not exist");
        $petriRepo = $this->container->get(PetrinetRepo::class);
        if (!$petriRepo->petrinetExists($pid))
            throw new Exception("Petri net does not exist");
        $petrinet = $petriRepo->getPetrinet($pid);
        // if (is_null($petrinet->getInitial())) {
        //     $message = "The Petri net has no initial marking. "
        //              . "Therefore, reachability and coverability "
        //              . "analysis are not possible.";
        //     throw new Exception($message);
        // }
        $jsonGraph = $request->getParsedBody();
        $converter = new JsonToGraph2($jsonGraph, $petrinet);
        $graph = $converter->convert();
        // var_dump($graph);
        // exit;
        $markings = $petriRepo->getMarkings($pid);
        $marking = $petriRepo->getMarking($markings[0]["id"], $petrinet);
        $marked = new MarkedPetrinet($petrinet, $marking);
        $checker = new CheckCoverabilityGraph($graph, $marked);
        $feedback = $checker->check();
        var_dump($feedback);
        exit;

        $sessionRepo = $this->container->get(SessionRepo::class);
        if ($sessionRepo->appendGraph($user, $sid, $graph) === FALSE)
            throw new Exception("Could not append graph to log");
        return $response->withJson($feedback);
    }
}
