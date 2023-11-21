<?php

namespace Cora\SystemChecker;

use Cora\Utils\Printer;

use Cora\Domain\Feedback\Feedback;
use Cora\Domain\Graph\GraphInterface as IGraph;
use Cora\Domain\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Domain\Petrinet\Transition\Transition;
use Cora\Domain\Petrinet\Marking\MarkingInterface as IMarking;

use Cora\Utils\SetUtils;

use \Ds\Queue as Queue;
use \Ds\Set as Set;
use \Ds\Map as Map;

class CheckCoverabilityGraph {
    protected $graph;
    protected $petrinet;
    protected $initial;

    public function __construct(
        IGraph $graph,
        IPetrinet $petrinet,
        ?IMarking $initial
    ) {
        $this->graph    = $graph;
        $this->petrinet = $petrinet;
        $this->initial  = $initial;
    }

    public function check() {
        $petrinet = $this->petrinet;
        $initialP = $this->initial;
        $graph    = $this->graph;

        $feedback = new Feedback();
        // check the initial marking
        $initialId = $graph->getInitial();
        // no initial marking supplied
        if (is_null($initialId)) {
            $feedback->add(Feedback::NO_INITIAL_STATE);
            return $feedback;
        }
        $initialG = $graph->getVertex($initialId);
        // compare initial markings
        if (!$initialG->equals($initialP)) {
            $feedback->add(Feedback::INCORRECT_INITIAL_STATE);
            if (!$initialG->unbounded()->isEmpty())
                $feedback->add(Feedback::OMEGA_IN_INITIAL);
            return $feedback;
        }
        $feedback->add(Feedback::CORRECT_INITIAL_STATE);
        $feedback->add(Feedback::REACHABLE_FROM_PRESET, $graph->getInitial());
        // maintain a map marking -> Set<id> to find all the vertexes belonging
        // to a marking, to detect duplicates
        $discovered = new Map();
        foreach($graph->getVertexes() as $marking){
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
            $currentMarking = $graph->getVertex($currentId);
            // map of reachable markings according to the Petri net
            $reachable      = $petrinet->reachable($currentMarking);
            // map of reachable markings according to the graph
            $postset        = $graph->postset($currentId);
            // add the current id to the discovery set for the
            // corresponding marking
            $discovered->get($currentMarking)->add($currentId);
            // get the set of all enabled transtions from the current marking
            $enabled = $reachable->transitions();
            // maintain a set of all fired transitions from the current node
            $fired = new Set();
            // add feedback for all the nodes in the postset
            foreach($postset as $id => $edge) {
                // add postset to frontier
                if (!$black->contains($edge->getTo()) &&
                    !$grey->contains($edge->getTo())) {
                    $queue->push($edge->getTo());
                    $grey->add($edge->getTo());
                }
                $firedTransition = new Transition($edge->getLabel());
                // mark duplicate edges
                if ($fired->contains($firedTransition)) {
                    $feedback->add(Feedback::DUPLICATE_EDGE, $id);
                    foreach($postset as $eid => $ped) {
                        if($edge->getLabel() == $ped->getLabel()) {
                            $feedback->add(Feedback::DUPLICATE_EDGE, $eid);
                        }
                    }
                }
                // add the current transition the the fired set
                $fired->add($firedTransition);
                // what marking has been discovered from the current one
                $discoveredMarking = $graph->getVertex($edge->getTo());
                // the set of unbounded places in the discovered marking
                $unbounded = $discoveredMarking->unbounded();
                // as a Ds\Set
                $unboundedSet = $unbounded->toSet();
                // can the transtion (label) fire?
                $isEnabled = $enabled->contains($firedTransition);
                // discovered marking is reachable with correct label
                // according to the Petri net. Does include self
                // loops. (Path without omega substitution)
                $directPath = $isEnabled && $reachable->get($firedTransition)
                                                      ->equals($discoveredMarking);
                if ($directPath) {
                    $equal = $currentMarking->equals($discoveredMarking);
                    if (($equal && $edge->getTo() == $edge->getFrom()) || !$equal) {
                        $feedback->add(Feedback::ENABLED_CORRECT_POST, $id);
                        $feedback->add(Feedback::REACHABLE_FROM_PRESET, $edge->getTo());
                    } else
                        $feedback->add(Feedback::MISSED_SELF_LOOP, $id);
                    // determine whether some places could be marked
                    // unbounded. Not needed for self loops
                    if (!$equal && !$black->contains($edge->getTo())) {
                        $coverable = $this->getCoverable($edge->getTo(), $discoveredMarking);
                        if (SetUtils::isStrictSubset($unboundedSet, $coverable))
                            $feedback->add(Feedback::OMEGA_OMITTED, $edge->getTo());
                    }
                    continue;
                }
                // correct post marking
                $correctPost = false;
                $omegaOmitted = false;
                $baseMarking = NULL;
                // correct edge
                $correctEdge = false;
                $requireLoop = false;
                foreach($reachable as $t => $m) {
                    // only update correct post when it is not correct (yet).
                    if (!$correctPost) {
                        // determine whether the edge should have been a self loop.
                        $requireLoop = $m->equals($currentMarking);
                        // determine if the marking is reachable
                        // immediately from the Petri net.
                        $correctPost = $m->equals($discoveredMarking) && !$requireLoop;
                        // get the set of coverable places from the current loop marking.
                        $coverable = $this->getCoverable($edge->getTo(), $m);
                        if (!$correctPost && !$requireLoop) {
                            // still not correct. Marking can now only
                            // be correct if \omega substitution takes
                            // place, and is performed correctly.
                            // Determine what the marking loop should
                            // look like with places unbounded as
                            // marked by the discovered marking
                            $replacement = $m->withUnbounded($petrinet, $unbounded);
                            // // the replacement is valid if it's equal
                            // to the discovered marking and there are
                            // no places that should not be marked unbounded
                            $correctPost = $replacement->equals($discoveredMarking) &&
                                         SetUtils::isSubset($unboundedSet, $coverable);
                        }
                        if ($correctPost) {
                            // if the post marking is now correct we
                            // determine whether places have been unmarked
                            $omegaOmitted = SetUtils::isStrictSubset(
                                $unboundedSet, $coverable);
                            $baseMarking = $m;
                        }

                    }
                    if (!$correctEdge && !is_null($baseMarking)) {
                        // an edge can only be fully correct if the
                        // post marking is correct. An edge is only
                        // correct of the label corresponds and firing
                        // the transition results in the loop marking.
                        $correctEdge = $correctPost &&
                                     $t->getID() == $edge->getLabel() &&
                                     $reachable->get($t)->equals($baseMarking) &&
                                     !$requireLoop;
                    }
                    if ($correctPost && $correctEdge)
                        break;
                }
                if ($correctPost) {
                    $feedback->add(Feedback::REACHABLE_FROM_PRESET, $edge->getTo());
                    if ($omegaOmitted)
                        $feedback->add(Feedback::OMEGA_OMITTED, $edge->getTo());
                } else {
                    $pre = $this->unboundedFromPreset($edge->getTo());
                    if (SetUtils::isStrictSubset($unbounded->toSet(), $pre))
                        $feedback->add(Feedback::OMEGA_FROM_PRESET_OMITTED,
                                       $edge->getTo());
                    else
                        $feedback->add(Feedback::NOT_REACHABLE_FROM_PRESET,
                                       $edge->getTo());
                }

                if ($correctEdge)
                    $feedback->add(Feedback::ENABLED_CORRECT_POST, $id);
                else if ($correctPost && $isEnabled && !$requireLoop)
                    $feedback->add(Feedback::ENABLED_CORRECT_POST_WRONG_LABEL, $id);
                else if ($correctPost && $isEnabled && $requireLoop)
                    $feedback->add(Feedback::MISSED_SELF_LOOP, $id);
                else if ($correctPost && !$isEnabled)
                    $feedback->add(Feedback::DISABLED_CORRECT_POST, $id);
                else if (!$correctPost && $isEnabled)
                    $feedback->add(Feedback::ENABLED_INCORRECT_POST, $id);
                else 
                    $feedback->add(Feedback::DISABLED, $id);
            }
            // get the difference between the enabled and fired set
            $transDiff = $enabled->toSet()->diff($fired);
            // if the difference is not empty, one or more transitions
            // have not been fired from the current marking while they
            // should have been
            if(!$transDiff->isEmpty())
                $feedback->add(Feedback::EDGE_MISSING, $currentId);
            // update bfs variables
            $grey->remove($currentId);
            $black->add($currentId);
        }
        // mark unreachable states
        $unreachable = $graph->getVertexes()->getIds()->diff($black);
        foreach($unreachable as $id) {
            $feedback->add(Feedback::NOT_REACHABLE_FROM_INITIAL, $id);
        }
        // mark duplicate states
        foreach($graph->getVertexes() as $id => $marking)
            if ($discovered->get($marking)->count() > 1)
                foreach($discovered->get($marking) as $element)
                    $feedback->add(Feedback::DUPLICATE_STATE, $element);
        return $feedback;
    }

    protected function getCoverable(
        int $current,
        ?IMarking $marking=NULL,
        ?Set $covered=NULL,
        ?Set $visited=NULL)
    {
        $graph = $this->graph;
        if (is_null($marking))
            $marking = $graph->getVertex($current);
        if (is_null($covered)) 
            $covered = new Set();
        if (is_null($visited))
            $visited = new Set();
        $visited->add($current);
        $preset = $graph->preset($current);
        if($preset->isEmpty())
            return $covered;
        $petrinet = $this->petrinet;
        foreach($preset as $edge) {
            if(!$visited->contains($edge->getFrom())) {
                $presetMarking = $graph->getVertex($edge->getFrom());
                if($marking->covers($presetMarking, $petrinet))
                    foreach($petrinet->getPlaces() as $place)
                        if($marking->get($place)->greater($presetMarking->get($place)))
                            $covered->add($place);
                $c = $this->getCoverable($edge->getFrom(), $marking, $covered, $visited);
                $covered = $covered->union($c);
            }
        }
        return $covered;
    }

    protected function unboundedFromPreset($id) {
        $graph     = $this->graph;
        $preset    = $graph->preset($id);
        $unbounded = new Set();
        foreach($preset as $eid => $edge) {
            if ($edge->getFrom() !== $edge->getTo()) {
                $marking   = $graph->getVertex($edge->getFrom());
                $unbounded = $unbounded->union($marking->unbounded()->toSet());
            }
        }
        return $unbounded;
    }
}
