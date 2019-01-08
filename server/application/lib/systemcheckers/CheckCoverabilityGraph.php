<?php

namespace Cora\SystemCheckers;

use \Cora\Systems as Systems;
use \Cora\Feedback\Feedback as Feedback;
use \Cora\Feedback\FeedbackCode as FeedbackCode;

use \Cora\Utils as Utils;

use \Ds\Queue as Queue;
use \Ds\Set as Set;
use \Ds\Map as Map;

class CheckCoverabilityGraph extends SystemChecker
{
    public $petrinet;

    public function __construct($graph, $petrinet) {
        parent::__construct($graph);
        $this->petrinet = $petrinet;
    }

    public function check() {
        $petrinet = $this->petrinet;
        $graph    = $this->system;

        $feedback = new Feedback();
        // check the initial marking
        $initial  = $graph->getKey($graph->getInitial());
        // no initial marking supplied
        if(is_null($initial)) {
            $feedback->add(FeedbackCode::NO_INITIAL_STATE);
            return $feedback;
        }
        // compare initial markings
        $initialMarking = $petrinet->getInitial();
        if(!$initial->equals($initialMarking)) {
            $feedback->add(FeedbackCode::INCORRECT_INITIAL_STATE);
            return $feedback;
        }
        $feedback->add(FeedbackCode::CORRECT_INITIAL_STATE);
        $feedback->add(FeedbackCode::REACHABLE_FROM_PRESET, $graph->getInitial());
        // maintain a map marking -> Set<id> to find all the vertexes belonging
        // to a marking, to detect duplicates
        $discovered = new Map();
        foreach($graph->getVertexes() as $id => $marking) {
            $discovered->put($marking, new Set());
        }
        // init variables for bfs
        $grey  = new Set();
        $black = new Set();
        $queue = new Queue();
        $queue->push($graph->getInitial());
        $grey->add($graph->getInitial());
        // start bfs
        while(!$queue->isEmpty()) {
            // retrieve the current node of the graph
            $currentId      = $queue->pop();
            $currentMarking = $graph->getKey($currentId);
            // map of reachable markings according to the Petri net
            $reachable      = $petrinet->reachable($currentMarking);
            // map of reachable markings according to the graph
            $postset        = $graph->postset($currentId);

            // add the current id to the discovery set for the
            // corresponding marking
            $discovered->get($currentMarking)->add($currentId);
            // get the set of all enabled transtions from the current marking
            $enabled = $reachable->keys();
            // maintain a set of all fired transitions from the current node
            $fired = new Set();

            // add feedback for all the nodes in the postset
            foreach($postset as $id => $edge) {
                // add postset to frontier
                if(!$black->contains($edge->to) && !$grey->contains($edge->to)) {
                    $queue->push($edge->to);
                    $grey->add($edge->to);
                }
                // mark duplicate edges
                if($fired->contains($edge->label)) {
                    $feedback->add(FeedbackCode::DUPLICATE_EDGE, $id);
                    foreach($postset as $eid => $ped) {
                        if($edge->label == $ped->label) {
                            $feedback->add(FeedbackCode::DUPLICATE_EDGE, $eid);
                        }
                    }
                }
                // add the current label the the fired set
                $fired->add($edge->label);
                // what marking has been discovered from the current one
                $discoveredMarking = $graph->getKey($edge->to);
                // can the transtion (label) fire?
                $isEnabled = $enabled->contains($edge->label);
                // discovered marking is reachable with correct label
                // does not include self loops
                if ($isEnabled && !$discoveredMarking->equals($currentMarking) &&
                    $reachable->get($edge->label)->equals($discoveredMarking)) {
                    $feedback->add(FeedbackCode::ENABLED_CORRECT_POST, $id);
                    $feedback->add(FeedbackCode::REACHABLE_FROM_PRESET, $edge->to);
                    continue;
                }
                // self loops
                if ($discoveredMarking->equals($currentMarking)) {
                    $isEnabled = $enabled->contains($edge->label);
                    $correctPost = $isEnabled &&
                        $reachable->get($edge->label)->equals($discoveredMarking);

                    // correctPost and self loop
                    if ($correctPost && $edge->from == $edge->to) {
                        $feedback->add(FeedbackCode::ENABLED_CORRECT_POST, $id);
                    }
                    // correctPost but no self loop
                    else if($correctPost) {
                        $feedback->add(FeedbackCode::MISSED_SELF_LOOP, $id);
                    }
                    else {
                        if ($isEnabled) {
                            $feedback->add(FeedbackCode::ENABLED_INCORRECT_POST, $id);
                        } else {
                            $feedback->add(FeedbackCode::DISABLED, $id);
                        }
                    }
                    continue;
                }
                // since the Petri net does not know the history of the graph the
                // Petri net cannot generate markings with new places marked as
                // unbounded. Therefore, we need to find a replacement for the
                // marking generated by the Petri net by replacing each token count
                // with an omega identifier as provided by the graph, but only when
                // this is correct.
                $correctTransition = false;
                // get the set of places that are marked as unbounded by the
                // discovered marking
                $unbounded = $discoveredMarking->unbounded();
                // get the marking that the discovered marking is based on
                $petrinetMarking = null;
                foreach($reachable as $t => $m) {
                    $mub = $m->markUnbounded($petrinet, $unbounded);
                    if($mub->equals($discoveredMarking)) {
                        $petrinetMarking = $m;
                        $correctTransition = $t == $edge->label;
                        break;
                    }
                }
                // get the set of places that could be marked as unbounded
                // according to the marking from the Petri net
                $coverable = is_null($petrinetMarking) ? new Set() :
                             $this->getCoverable($edge->to, $petrinetMarking);
                $unboundedPreset = $this->unboundedFromPreset($edge->to);

                $correctMarking = !is_null($petrinetMarking) &&
                                  Utils\SetUtils::isSubset($unboundedPreset, $unbounded) &&
                                  Utils\SetUtils::isSubset($coverable, $unbounded);
                // give feedback to the discovered marking
                if($correctMarking) {
                    $feedback->add(FeedbackCode::REACHABLE_FROM_PRESET, $edge->to);
                } else {
                    $feedback->add(FeedbackCode::NOT_REACHABLE_FROM_PRESET, $edge->to);
                }
                if($correctTransition) {
                    $feedback->add(FeedbackCode::ENABLED_CORRECT_POST, $id);
                } else if($isEnabled && $correctMarking) {
                    $feedback->add(FeedbackCode::ENABLED_CORRECT_POST_WRONG_LABEL, $id);
                } else if($isEnabled) {
                    $feedback->add(FeedbackCode::ENABLED_INCORRECT_POST, $id);
                } else if($correctMarking) {
                    $feedback->add(FeedbackCode::DISABLED_CORRECT_POST, $id);
                } else {
                    $feedback->add(FeedbackCode::DISABLED, $id);
                }
            }
            // get the difference between the enabled and fired set
            $transDiff = $enabled->diff($fired);
            // if the difference is not empty, one or more transitions
            // have not been fired from the current marking while they
            // should have been
            if(!$transDiff->isEmpty()) {
                $feedback->add(FeedbackCode::EDGE_MISSING, $currentId);
            }
            // update bfs variables
            $grey->remove($currentId);
            $black->add($currentId);
        }
        // mark unreachable states
        foreach($graph->getVertexes() as $id => $marking) {
            if(!$marking->reachable) {
                $feedback->add(FeedbackCode::NOT_REACHABLE_FROM_INITIAL, $id);
            }
        }
        // mark duplicates
        foreach($graph->getVertexes() as $id => $marking) {
            if($discovered->get($marking)->count() > 1) {
                foreach($discovered->get($marking) as $element) {
                    $feedback->add(FeedbackCode::DUPLICATE_STATE, $element);
                }
            }
        }
        return $feedback;
    }

    protected function getCoverable($current, $marking, $covered=[], $visited=[]) {
        if(is_array($covered)) {
            $covered = new Set();
        }
        if(is_array($visited)) {
            $visited = new Set();
        }
        $graph = $this->system;
        $visited->add($current);
        $preset = $graph->preset($current);
        if($preset->isEmpty()) { 
            return $covered;
        }
        foreach($preset as $id => $edge) {
            if(!$visited->contains($edge->from)) {
                $presetMarking = $graph->getKey($edge->from);
                if($marking->covers($presetMarking, $this->petrinet)) {
                    foreach($this->petrinet->getPlaces() as $i => $place) {
                        if($marking->get($place)->greater($presetMarking->get($place))) {
                            $covered->add($place);
                        }
                    }
                }
                $c = $this->getCoverable($edge->from, $marking, $covered, $visited);
                $covered = $covered->union($c);
            }
        }
        return $covered;
    }

    protected function unboundedFromPreset($id) {
        $graph     = $this->system;
        $preset    = $graph->preset($id);
        $unbounded = new Set();
        foreach($preset as $id => $edge) {
            $marking   = $graph->getKey($edge->from);
            $unbounded = $unbounded->union($marking->unbounded());
        }
        return $unbounded;
    }
}

?>
