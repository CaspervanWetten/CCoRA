<?php

namespace Cora\SystemCheckers;

use \Cora\Systems as Systems;
use \Cora\Feedback\Feedback as Feedback;
use \Cora\Feedback\FeedbackCode as FeedbackCode;

use \Cora\Utils as Utils;
use \Cora\Utils\SetUtils;

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
            if(!$initial->unbounded()->isEmpty()) {
                $feedback->add(FeedbackCode::OMEGA_IN_INITIAL);
            }
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
                // the set of unbounded places in the discovered marking
                $unbounded = $discoveredMarking->unbounded();
                // can the transtion (label) fire?
                $isEnabled = $enabled->contains($edge->label);
                // discovered marking is reachable with correct label
                // according to the Petri net. Does include self
                // loops. (Path without omega substitution)
                if ($isEnabled && $reachable->get($edge->label)->equals($discoveredMarking)) {
                    $equal = $currentMarking->equals($discoveredMarking);
                    if (($equal && $edge->to == $edge->from) || !$equal) {
                        $feedback->add(FeedbackCode::ENABLED_CORRECT_POST, $id);
                        $feedback->add(FeedbackCode::REACHABLE_FROM_PRESET, $edge->to);
                    } else {
                        $feedback->add(FeedbackCode::MISSED_SELF_LOOP, $id);
                    }
                    // determine whether some places could be marked
                    // unbounded. Not needed for self loops
                    if (!$equal && !$black->contains($edge->to)) {
                        $coverable = $this->getCoverable($edge->to, $discoveredMarking);
                        if (Utils\SetUtils::isStrictSubset($unbounded, $coverable)) {
                            $feedback->add(FeedbackCode::OMEGA_OMITTED, $edge->to);
                        }
                    }
                    continue;
                }
                // correct post marking
                $correctPost = false;
                $omegaOmitted = false;
                $omegaPresetOmitted = false;
                // correct edge
                $correctEdge = false;
                $evadedLoop = false;
                foreach($reachable as $t => $m) {
                    // only update correct post when it is not correct (yet).
                    if (!$correctPost) {
                        // determine if the marking is reachable
                        // immediately from the Petri net.
                        $correctPost = $m->equals($discoveredMarking);
                        // get the set of coverable places from the current loop marking.
                        $coverable = $this->getCoverable($edge->to, $m);
                        // determine whether the edge should have been a self loop.
                        $evadedLoop = $m->equals($currentMarking);
                        if (!$correctPost) {
                            // still not correct. Marking can now only
                            // be correct of \omega substitution takes
                            // place, and is performed correctly.
                            $addedOmega = Utils\SetUtils::isStrictSuperset($unbounded, $m->unbounded());
                            // determine what the marking loop should
                            // look like with places unbounded as
                            // marked by the discovered marking
                            $replacement = $m->markUnbounded($petrinet, $unbounded);
                            // the replacement is valid if it's equal
                            // to the discovered marking and there are
                            // no places that should not be marked unbounded
                            $isValidReplacement = $replacement->equals($discoveredMarking) &&
                                                Utils\SetUtils::isSubset($unbounded, $coverable);
                            $correctPost = $addedOmega && $isValidReplacement && !$evadedLoop;
                        }
                        if ($correctPost) {
                            // if the post marking is now correct we
                            // determine whether places have been unmarked
                            $omegaOmitted = SetUtils::isStrictSubset($unbounded, $coverable);
                        }
                        if (!$correctEdge) {
                            // an edge can only be fully correct if
                            // the post marking is correct. An edge is
                            // only correct of the label corresponds
                            // and firing the transition results in
                            // the loop marking.
                            $correctEdge = $correctPost && $t == $edge->label &&
                                         $reachable->get($t)->equals($m) && !$evadedLoop;
                        }
                    }
                    if ($correctPost && $correctEdge) {
                        break;
                    }
                }

                if ($correctPost) {
                    $feedback->add(FeedbackCode::REACHABLE_FROM_PRESET, $edge->to);
                    if ($omegaOmitted) {
                        $feedback->add(FeedbackCode::OMEGA_OMITTED, $edge->to);
                    }
                } else {
                    $pre = $this->unboundedFromPreset($edge->to);
                    if (SetUtils::isStrictSubset($unbounded, $pre)) {
                        $feedback->add(FeedbackCode::OMEGA_FROM_PRESET_OMITTED, $edge->to);
                    } else {
                        $feedback->add(FeedbackCode::NOT_REACHABLE_FROM_PRESET, $edge->to);
                    }
                }

                if ($correctEdge) {
                    $feedback->add(FeedbackCode::ENABLED_CORRECT_POST, $id);
                } else if ($correctPost && $isEnabled && !$evadedLoop) {
                    $feedback->add(FeedbackCode::ENABLED_CORRECT_POST_WRONG_LABEL, $id);
                } else if ($correctPost && $isEnabled && $evadedLoop) {
                    $feedback->add(FeedbackCode::MISSED_SELF_LOOP, $id);
                } else if ($correctPost && !$isEnabled) {
                    $feedback->add(FeedbackCode::DISABLED_CORRECT_POST, $id);
                } else if (!$correctPost && $isEnabled) {
                    $feedback->add(FeedbackCode::ENABLED_INCORRECT_POST, $id);
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

    protected function getCoverable($current, $marking=NULL, $covered=[], $visited=[]) {
        if (is_null($marking)) {
            $marking = $this->system->getKey($current);
        }
        if (is_array($covered)) {
            $covered = new Set();
        }
        if (is_array($visited)) {
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
        foreach($preset as $eid => $edge) {
            if ($edge->from !== $edge->to) {
                $marking   = $graph->getKey($edge->from);
                $unbounded = $unbounded->union($marking->unbounded());
            }
        }
        return $unbounded;
    }
}

?>
