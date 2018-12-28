<?php

namespace Cozp\Systems\Petrinet;

use \Cozp\Systems as Systems;
use \Ds\Set as Set;
use \Ds\Map as Map;

class Petrinet
{
    public $places;         // set of strings
    public $transitions;    // set of strings
    public $flows;          // set of triples (from, to, weight)
    public $initialMarking; // set of pairs (place, tokens)

    public function __construct($places=null, $transitions=null, $flows=null, $initial = NULL)
    {
        $places = is_null($places) ? new Set() : $places;
        $transitions = is_null($transitions) ? new Set() : $transitions;
        $flows = is_null($flows) ? new Set() : $flows;

        $places = $places instanceof Set ? $places : new Set($places);
        $transitions = $transitions instanceof Set ? $transitions : new Set($transitions);
        $flows = $flows instanceof Set ? $flows : new Set($flows);
        
        $this->places = $places;
        $this->transitions = $transitions;
        $this->flows = $flows;
        $this->setInitialMarking($initial);
    }

    public function isEnabled($marking, $transition)
    {
        $preWeights = $this->getTransitionPreSetWeights($transition);

        $result = true;
        foreach($preWeights as $place=>$weight) {
            $m = $marking->get($place);
            $w = $weight;
            if ($m instanceof Systems\IntegerTokenCount && $m->value < $w) {
                $result = false;
                break;
            }
        }
        return $result;
    }

    public function fire($marking, $transition)
    {
        $arr = [];
        $pre = $this->getTransitionPreSetWeights($transition);
        $post = $this->getTransitionPostSetWeights($transition);
        
        foreach($marking as $place => $tokens) {
            $wpre = $wpost = 0;
            if($pre->hasKey($place)) $wpre = $pre->get($place);
            if($post->hasKey($place)) $wpost = $post->get($place);
            $newTokens = $tokens->add($wpost)->subtract($wpre)->__toString();
            $arr[$place] = $newTokens;
        }
        $m = new Systems\Marking($this, $arr);
        return $m;
    }

    public function enabledTransitions($marking)
    {
        $res = new Set();
        $transitions = $this->transitions;
        foreach($transitions as $i => $transition) {
            if ($this->isEnabled($marking, $transition)) {
                $res->add($transition);
            }
        }
        return $res;
    }

    /**
     * Get the markings which can be reached by firing all transitions from the
     * given marking. Only enabled transitions are fired.
     *
     * @param Marking $marking
     * @return Set
     */
    public function getMarkingPostSet($marking)
    {
        $result = new Set();
        $transitions = $this->getTransitions();
        foreach($transitions as $i => $transition) {
            if($this->isEnabled($marking, $transition)) {
                $newmarking = $this->fire($marking, $transition);
                $result->add($newmarking);
            }
        }
        return $result;
    }

    /**
     * Get the markings which can be reached by firing all transitions from the
     * given marking. Only enabled transitions are fired. For each reached
     * marking the fired transition is included.
     *
     * @param Marking $marking
     * @return Set
     */
    public function getMarkingPostSetWithTransition($marking)
    {
        $result = new Set();
        $transitions = $this->getTransitions();
        foreach($transitions as $i => $transition) {
            if($this->isEnabled($marking, $transition)) {
                $newMarking = $this->fire($marking, $transition);
                $result->add(new MarkingTransitionPair($newMarking, $transition));
            }
        }
        return $result;
    }

    /**
     * Get all the places in the preset of a supplied transition
     *
     * @param string $transition
     * @return Set
     */
    protected function getTransitionPreSet($transition)
    {
        $f = $this->getFlows();
        $s = new Set();
        foreach($f as $i => $flow) {
            if($flow->to == $transition) {
                $s->add($flow->from);
            }
        }
        return $s;
    }

    /**
     * Get all the places in the postset of a supplied transition
     *
     * @param string $transition
     * @return Set
     */
    protected function getTransitionPostSet($transition)
    {
        $f = $this->getFlows();
        $s = new Set();
        foreach($f as $i => $flow) {
            if($flow->from == $transition) {
                $s->add($flow->to);
            }
        }
        return $s;
    }

    /**
     * Get all incoming flows for the supplied transition
     *
     * @param string $transition
     * @return Set
     */
    protected function getTransitionPreSetFlows($transition)
    {
        $f = $this->getFlows();
        $s = [];
        foreach($f as $i => $flow) {
            if($flow->to == $transition) {
                array_push($s, $flow);
            }
        }
        return $s;
    }

    /**
     * Get all outgoing flows for the supplied transition
     *
     * @param string $transition
     * @return Set
     */
    protected function getTransitionPostSetFlows($transition)
    {
        $f = $this->getFlows();
        $s = [];
        foreach($f as $i => $flow) {
            if($flow->from == $transition) {
                array_push($s, $flow);
            }
        }
        return $s;
    }

    /**
     * Get the places in the preset of the transition with the weight of flow.
     * Returns a Map which is indexed by the place and maps to the weight.
     *
     * @param string $transition
     * @return Map
     */
    protected function getTransitionPreSetWeights($transition)
    {
        $f = $this->getFlows();
        $map = new Map();
        foreach($f as $i => $flow) {
            if($flow->to == $transition) {
                $map->put($flow->from, $flow->weight);
            }
        }
        return $map;
    }

    /**
     * Get the places in the postset of the transition with the weight of the flow.
     * Returns a Map which is indexed by the places and maps to the weights.
     *
     * @param string $transition
     * @return Map
     */
    protected function getTransitionPostSetWeights($transition)
    {
        $f = $this->getFlows();
        $map = new Map();
        foreach($f as $i => $flow) {
            if($flow->from == $transition) {
                $map->put($flow->to, $flow->weight);
            }
        }
        return $map;
    }

    // GETTERS AND SETTERS

    public function getPlaces()
    {
        return $this->places;
    }

    public function setPlaces($places)
    {
        $s = $places instanceof Set ? $places : new Set($places);
        $this->places = $s;
    }

    public function getTransitions()
    {
        return $this->transitions;
    }

    public function setTransitions($transitions)
    {
        $t = $transitions instanceof Set ? $transitions : new Set($transitions);
        $this->transitions = $t;
    }

    public function getFlows()
    {
        return $this->flows;
    }

    public function SetFlows($flows)
    {
        $f = $flows instanceof Set ? $flows : new Set($flows);
        $this->flows = $f;
    }

    public function getInitialMarking()
    {
        return $this->initialMarking;
    }

    public function setInitialMarking($marking)
    {
        $marking = new Systems\Marking($this, $marking);
        $this->initialMarking = $marking;
    }
}

class MarkingTransitionPair implements \Ds\Hashable
{
    public $marking;
    public $transition;
    public function __construct($marking, $transition)
    {
        $this->marking = $marking;
        $this->transition = $transition;
    }

    public function hash()
    {
        return sprintf("(%s, %s)", $this->marking, $this->transition);
    }

    public function equals($other):bool{
        return $this->hash() == $other->hash();
    }
}

?>
