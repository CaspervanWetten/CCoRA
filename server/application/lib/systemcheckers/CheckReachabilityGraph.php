<?php

namespace Cora\SystemCheckers;

use \Cora\Systems as Systems;
use \Cora\Feedback as Feedback;
use \Cora\Enumerators\GraphSetType as GraphSetType;

use \Ds\Queue as Queue;
use \Ds\Map as Map;
use \Ds\Set as Set;

class ReachabilityTriple
{
    public $from;
    public $to;
    public $via;
    public function __construct($from, $to, $via)
    {
        $this->from = $from;
        $this->to = $to;
        $this->via = $via;
    }
}

class VertexTransitionPair implements \Ds\Hashable
{
    public $vertex;
    public $transition;
    public function __construct($vertex, $transition)
    {
        $this->vertex = $vertex;
        $this->transition = $transition;
    }

    public function hash()
    {
        return sprintf("(%s, %s)", $this->vertex, $this->transition);
    }

    public function equals($other):bool {
        return $this->hash() == $other->hash();
    }
}

class CheckReachabilityGraph extends SystemChecker
{
    public $petrinet;
    
    public function __construct($graph, $petrinet)
    {
        parent::__construct($graph);
        $this->petrinet = $petrinet;
    }

    public function check()
    {
        $petrinet = $this->petrinet;
        $graph    = $this->system;

        $initialMarking = $petrinet->getInitialMarking();
        $initialVertex  = $graph->getInitialVertex();
        
        $feedback = new Feedback\Feedback();
        // // no initial state supplied, nowhere to go from here
        // if(!isset($initialVertex)) {
        //     $feedback->addFeedback(Feedback\FeedbackCode::NO_INITIAL_STATE);
        //     return $feedback;
        // }
        // // wrong initial state, cut off.
        // if(!$initialMarking->equals($graph->getVertex($initialVertex)))
        // {
        //     $feedback->addFeedback(Feedback\FeedbackCode::INCORRECT_INITIAL_STATE);
        //     return $feedback;
        // }
        // // correct initial state, continue
        // $feedback->addFeedback(Feedback\FeedbackCode::CORRECT_INITIAL_STATE);
        // $feedback->addFeedback(Feedback\FeedbackCode::CORRECT_STATE, $initialVertex);
        
        // $grey = new Set();
        // $black = new Set();
        // $queue = new Queue();

        // $queue->push($graph->getInitialVertex());
        // $grey->add($graph->getInitialVertex());

        // while(!$queue->isEmpty()) {
        //     $currentVertex = $queue->pop();
        //     $currentMarking = $graph->getVertex($currentVertex);

        //     $reachables = $petrinet->getMarkingPostSetWithTransition($currentMarking);
        //     $post = $graph->getPostSet($currentVertex, GraphSetType::Edge);
        //     foreach ($post as $edgeId => $edge) {
        //         $to = $edge->toId;
        //         $label = $edge->label;
        //         $discoveredMarking = $graph->getVertex($to);

        //         $pair = new \Cora\Systems\Petrinet\MarkingTransitionPair($discoveredMarking->asMarking($petrinet), $label);
        //         // correct
        //         if ($reachables->contains($pair)) {
        //             $reachables->remove($pair);
        //             $feedback->addFeedback(Feedback\FeedbackCode::CORRECT_EDGE, $edgeId);
        //             $feedback->addFeedback(Feedback\FeedbackCode::CORRECT_STATE, $to);
        //         }
        //         //incorrect
        //         else {
        //             $discoveredMarkingIsReachable = false;
        //             // look if marking is reachable but wrong transition label
        //             foreach($reachables as $i => $reachable) {
        //                 if($discoveredMarking->asMarking($petrinet) == $reachable->marking) {
        //                     // marking is reachable
        //                     $feedback->addFeedback(Feedback\FeedbackCode::INCORRECT_EDGE, $edgeId);
        //                     $feedback->addFeedback(Feedback\FeedbackCode::CORRECT_STATE, $to);
        //                     $discoveredMarkingIsReachable = true;
        //                     $reachables->remove($reachable);
        //                 }
        //             }
        //             if(!$discoveredMarkingIsReachable) {
        //                 // marking is not reachable (and has not been visited yet)
        //                 if(!$black->contains($to) && !$grey->contains($to)) {
        //                     $feedback->addFeedback(Feedback\FeedbackCode::INCORRECT_STATE, $to);
        //                 }
        //                 foreach($reachables as $i => $reachable) {
        //                     if($reachable->transition == $edge->label) {
        //                         $feedback->addFeedback(Feedback\FeedbackCode::INCORRECT_EDGE, $edgeId);
        //                         $reachables->remove($reachable);
        //                     }
        //                 }
        //             }
        //         }
        //         if(!$grey->contains($to) && !$black->contains($to)) {
        //             $grey->add($to);
        //             $queue->push($to);
        //         }
        //     }

        //     $black->add($currentVertex);
        //     $grey->remove($currentVertex);

        //     if(!$reachables->isEmpty()) {
        //         $feedback->addFeedback(Feedback\FeedbackCode::EDGE_MISSING, $currentVertex);
        //     }
        // }
        // $allGraphMarkings = $graph->getVertexes();
        // if(count($allGraphMarkings) != count($black)) {
        //     foreach($allGraphMarkings as $vertex => $marking) {
        //         if(!$black->contains($vertex)) {
        //             $feedback->addFeedback(Feedback\FeedbackCode::INCORRECT_STATE, $vertex);
        //         }
        //     }
        // }

        return $feedback;
    }
}
?>
