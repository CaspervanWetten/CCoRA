<?php

namespace Cora\SystemCheckers;

use \Cora\Feedback as Feedback;
use \Cora\Systems as Systems;
use \Cora\Enumerators\GraphSetType as GraphSetType;

use \Ds\Queue as Queue;
use \Ds\Set as Set;
use \Ds\Map as Map;

class CheckCoverabilityGraph extends SystemChecker
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

        // no initial state supplied, nowhere to go from here
        if(!isset($initialVertex)) {
            $feedback->addFeedback(Feedback\FeedbackCode::NO_INITIAL_STATE);
            return $feedback;
        }
        // wrong initial state, cut off.
        if(!$initialMarking->equals($graph->getVertex($initialVertex)))
        {
            $feedback->addFeedback(Feedback\FeedbackCode::INCORRECT_INITIAL_STATE);
            return $feedback;
        }
        // correct initial state, continue
        $feedback->addFeedback(Feedback\FeedbackCode::CORRECT_INITIAL_STATE);
        $feedback->addFeedback(Feedback\FeedbackCode::REACHABLE_FROM_PRESET, $initialVertex);

        // maintain sets of states that are visited and should still be visited
        $grey = new Set();
        $black = new Set();
        $queue = new Queue();
        
        // maintain a list of markings we have discovered, to detect duplicates
        $discoveredMarkings = new Map();
        $vertexes = $graph->getVertexes();
        foreach($vertexes as $i => $vertex) {
            $discoveredMarkings->put($vertex, new Set());
        }

        // start with the initial marking
        $queue->push($initialVertex);
        $grey->add($initialVertex);

        while(!$queue->isEmpty())
        {            
            $currentVertex  = $queue->pop();
            $currentMarking = $graph->getVertex($currentVertex);
            $reachables     = $petrinet->getMarkingPostSetWithTransition($currentMarking);
            $post           = $graph->getPostSet($currentVertex, GraphSetType::Edge);
         
            $s = $discoveredMarkings->get($currentMarking);
            $s->add($currentVertex);
            $discoveredMarkings->put($currentMarking, $s);

            // which transitions are enabled
            $enabledTransitions = $petrinet->enabledTransitions($currentMarking);
            $firedTransitions = new Set();

            foreach($post as $edgeId => $edge) {
                $to = $edge->toId;
                $label = $edge->label;
                $discoveredMarking = $graph->getVertex($to);

                $pair = new \Cora\Systems\Petrinet\MarkingTransitionPair($discoveredMarking->asMarking($petrinet), $label);
                // correct
                // no new unbounded places detected
                if ($reachables->contains($pair)) {
                    $feedback->addFeedback(Feedback\FeedbackCode::ENABLED_CORRECT_POST, $edgeId);
                    $feedback->addFeedback(Feedback\FeedbackCode::REACHABLE_FROM_PRESET, $to);
                }
                else {
                    // which places are marked as covered by the discovered marking
                    $up = $pair->marking->unboundedPlaces();
                    
                    $correctMarking = $discoveredMarking;
                    if($enabledTransitions->contains($label))
                        $correctMarking = $petrinet->fire($currentMarking, $label);
                    else {
                        foreach($enabledTransitions as $i => $trans) {
                            $m = $petrinet->fire($currentMarking, $trans);
                            if($m->markUnbounded($petrinet, $up) == $discoveredMarking->asMarking($petrinet)) {
                                $correctMaking = $m;
                                break;
                            }
                        }
                    }
                    // enforce loop
                    $skip = false;
                    if($correctMarking == $currentMarking->asMarking($petrinet) && $enabledTransitions->contains($label)) {
                        if($discoveredMarking == $currentMarking) {
                            if($enabledTransitions->contains($label)) {
                                $feedback->addFeedback(Feedback\FeedbackCode::ENABLED_CORRECT_POST, $edgeId);
                            }
                        }
                        else {
                            $feedback->addFeedback(Feedback\FeedbackCode::ENABLED_INCORRECT_POST, $edgeId);
                        }
                        $skip = true;
                    }

                    $coverable = $this->getCoverable($to, $correctMarking);
                    // which places are marked as covered by the preset
                    $coveredByParents = $this->getUnboundedPlacesByPreset($to);
                    // are the places marked as covered actually coverable
                    // assume yes
                    $placesAreCoverable = true;
                    foreach($up as $i => $place) {
                        if(!$coverable->contains($place)) {
                            $placesAreCoverable = false;
                            break;
                        }
                    }
                    // new set of unbounded places should be a super set or equal
                    // to the set of unbounded places marked by the preset.
                    $diff = $coveredByParents->diff($up);
                    // have we found a replacement for a reachable marking
                    $found = false;
                    if($placesAreCoverable && $diff->isEmpty()){
                        foreach($reachables as $i => $reachable) {
                        // correctly marked a set of places as unbounded.
                            if($reachable->marking->markUnbounded($petrinet, $up) == $pair->marking) {
                                // correct (=reachable) state
                                $found = true;
                                $feedback->addFeedback(Feedback\FeedbackCode::REACHABLE_FROM_PRESET, $to);
                                if($pair->transition == $reachable->transition && !$skip) {
                                    // label correct
                                    $feedback->addFeedback(Feedback\FeedbackCode::ENABLED_CORRECT_POST, $edgeId);
                                    $feedback->removeFeedback(Feedback\FeedbackCode::ENABLED_CORRECT_POST_WRONG_LABEL, $edgeId);
                                }
                                else {
                                    // label not correct
                                    // is the transition denoted by the label enabled?
                                    if($enabledTransitions->contains($pair->transition) && !$skip) {
                                        // enabled
                                        $feedback->addFeedback(Feedback\FeedbackCode::ENABLED_CORRECT_POST_WRONG_LABEL, $edgeId);
                                    }
                                    else if (!$skip) {
                                        // disabled
                                        $feedback->addFeedback(Feedback\FeedbackCode::DISABLED_CORRECT_POST, $edgeId);
                                    }
                                }
                                // break;
                            }
                        }
                    }
                    if(!$found) {
                        // we have not found a reachable marking
                        $feedback->addFeedback(Feedback\FeedbackCode::NOT_REACHABLE_FROM_PRESET, $to);
                        // is the transition used enabled?
                        if($enabledTransitions->contains($pair->transition)) {
                            // enabled transition with the wrong post
                            $feedback->addFeedback(Feedback\FeedbackCode::ENABLED_INCORRECT_POST, $edgeId);
                        }
                        else {
                            // transition used is not enabled from the current marking
                            $feedback->addFeedback(Feedback\FeedbackCode::DISABLED, $edgeId);
                        }
                    }
                }
                
                if($firedTransitions->contains($pair->transition)) {
                    $feedback->addFeedback(Feedback\FeedbackCode::DUPLICATE_EDGE, $edgeId);
                    // mark all edges with this transition as duplicate
                    foreach($post as $eid => $edge) {
                        if($edge->label == $pair->transition) {
                            $feedback->addFeedback(Feedback\FeedbackCode::DUPLICATE_EDGE, $eid);
                        }
                    }
                }
                $firedTransitions->add($pair->transition);
                
                if(!$black->contains($to) && !$grey->contains($to)) {
                    $queue->push($to);
                    $grey->add($to);
                }
            }
            $black->add($currentVertex);
            $grey->remove($currentVertex);

            $transitiondiff = $enabledTransitions->diff($firedTransitions);

            if(!$transitiondiff->isEmpty()) {
                $feedback->addFeedback(Feedback\FeedbackCode::EDGE_MISSING, $currentVertex);
            }
        }

        $vertexes = $graph->getVertexes();
        foreach($vertexes as $i => $vertex) {
            if(!$vertex->reachableFromInitial) {
                $feedback->addFeedback(Feedback\FeedbackCode::NOT_REACHABLE_FROM_INITIAL, $i);
            }
            // check for duplicates
            if($discoveredMarkings->get($vertex)->count() > 1)   {
                $set = $discoveredMarkings->get($vertex);
                foreach($set as $j => $id) {
                    $feedback->addFeedback(Feedback\FeedbackCode::DUPLICATE_STATE, $id);
                }
            }
        }

        return $feedback;
    }

    protected function getCoverable($current, $directMarking, $vertex = NULL, $covered = [], $visited = [], $j = 1)
    {
        if(is_array($covered)) {
            $covered = new Set($covered);
        }
        if(is_array($visited)) {
            $visited = new Set($visited);
        }
        if(is_null($vertex)) {
            $vertex = $current;
        }
        $graph = $this->system;

        $visited->add($current);
        $preset = $graph->getPreSet($current, GraphSetType::Vertex);
        if($preset->isEmpty() || !$graph->getVertex($current)->reachableFromInitial) {
            return $covered;
        }
        else {
            foreach($preset as $i => $pre)
            {
                if(!$visited->contains($pre))
                {
                    $presetMarking = $graph->getVertex($pre);
                    if($directMarking->covers($presetMarking, $this->petrinet)) {
                        $places = $this->petrinet->getPlaces();
                        foreach($places as $i => $place) {
                            if($directMarking->get($place)->greater($presetMarking->get($place))) {
                                $covered->add($place);
                            }
                        }
                    }
                }
                $c = new Set();
                if(!$visited->contains($pre))
                    $c = $this->getCoverable($pre, $directMarking, $vertex, $covered, $visited);
                $covered = $covered->union($c);
            }
        }
        return $covered;
    }

    /**
     * Get all places that are marked unbounded by markings in the
     * preset of the supplied marking (supplied by vertex id
     *
     * @param int $vertex
     * @return Set places
     */
    protected function getUnboundedPlacesByPreset($vertex)
    {
        $graph = $this->system;
        $parents = $graph->getPreset($vertex, GraphSetType::Vertex);
        $result = new Set();
        foreach($parents as $i => $p) {
            $marking = $graph->getVertex($p);
            $result = $result->union($marking->unboundedPlaces());
        }
        return $result;
    }
}

?>
