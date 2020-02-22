<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Marking;

use Ds\Set as Set;
use Ds\Map as Map;

use Exception;

class Petrinet
{
    protected $places;         // set of strings
    protected $transitions;    // set of strings
    protected $flows;          // bag of flows ((P x T) U (T x P) -> N)
    protected $initialMarking; // set of pairs (place, tokens)

    public function __construct($places=null, $transitions=null, $flows=null, $initial = NULL)
    {
        $places      = is_null($places) ? new Set() : $places;
        $transitions = is_null($transitions) ? new Set() : $transitions;
        $flows       = is_null($flows) ? new Map() : $flows;

        $places      = $places instanceof Set ? $places : new Set($places);
        $transitions = $transitions instanceof Set ? $transitions : new Set($transitions);
        $flows       = $flows instanceof Map ? $flows : new Map($flows);

        $disjoint = ($places->intersect($transitions))->isEmpty();
        if(!$disjoint) {
            throw new \Exception("Petri net places and transitions are not disjoint");
        }

        $this->places = $places;
        $this->transitions = $transitions;
        $this->flows = $flows;
        if (!is_null($initial)) {
            $this->setInitial($initial);
        }
    }

   /**
    * Determine whether a transition is enabled given a marking
    * @param Marking $marking The marking in question
    * @param string $transition The transition to check
    * @return bool Whether the transition is enabled
    **/
    public function enabled($marking, $transition)
    {
        $preset = $this->preset($transition);

        $result = true;
        foreach($preset as $place => $weight) {
            $m = $marking->get($place);
            if ($m instanceof Tokens\IntegerTokenCount && $m->value < $weight) {
                $result = false;
                break;
            }
        }
        return $result;
    }

   /**
    * Fire a transition in a particular marking and get a new marking
    * The transition is _not_ checked for enabledness
    * @param Marking $marking The marking in question
    * @param string $transition The transition to fire
    * @return Marking The resulting marking
    **/
    public function fire($marking, $transition)
    {
        $arr = [];
        $pre = $this->preset($transition);
        $post = $this->postset($transition);
        
        foreach($marking as $place => $tokens) {
            $wpre = $pre->get($place, 0);
            $wpost = $post->get($place, 0);
            $newTokens = $tokens->add($wpost)->subtract($wpre);
            $arr[$place] = $newTokens;
        }
        $m = new Marking($this, $arr);
        return $m;
    }

   /**
    * Get a set of all enabled transitions for a given marking
    * @param Marking $marking The marking in question
    * @return Set The set of enabled markings
    **/
    public function enabledTransitions($marking)
    {
        $res = new Set();
        $transitions = $this->transitions;
        foreach($transitions as $i => $transition) {
            if ($this->enabled($marking, $transition)) {
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
     * @return Map Map: Transition -> Marking
     */
    public function reachable($marking)
    {
        $result = new Map();
        $transitions = $this->enabledTransitions($marking);
        foreach($transitions as $i => $t) {
            $newMarking = $this->fire($marking, $t);
            $result->put($t, $newMarking);
        }
        return $result;
    }

   /**
    * Get the preset of a particular element
    * @param string $el The element in question (place or transition)
    * @return Map Map: Element -> Weight
    **/
    public function preset($el) {
        $f = $this->flows;
        $s = new Map();
        foreach($f as $flow => $weight) {
            if($flow->to == $el) {
                $s->put($flow->from, $weight);
            }
        }
        return $s;
    }

   /**
    * Get the postset of a particular element
    * @param string $el The element in question (place or transition)
    * @return Map Map: Element -> weight
    **/
    public function postset($el) {
        $f = $this->flows;
        $s = new Map();
        foreach($f as $flow => $weight) {
            if($flow->from == $el) {
                $s->put($flow->to, $weight);
            }
        }
        return $s;
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

    public function setFlows($flows)
    {
        $f = $flows instanceof Map ? $flows : new Map($flows);
        $this->flows = $f;
    }

    public function getInitial()
    {
        return $this->initialMarking;
    }

    public function setInitial($marking)
    {
        $marking = new Marking($this, $marking);
        $this->initialMarking = $marking;
    }
}

?>
