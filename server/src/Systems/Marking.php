<?php

namespace Cora\Systems;

use Cora\Systems\Tokens;

use \Ds\Set as Set;

class Marking implements \JsonSerializable, \Iterator, \Ds\Hashable
{
    public $vector;

    public function __construct($petrinet, $list = []) {
        $this->vector = $this->listToVector($petrinet->getPlaces(), $list);
    }

   /**
    * Get the Systems\TokenCount object assigned to a particular place
    * @param string $place The place for which to retrieve the tokens
    * @return \Cora\Systems\TokenCount TokenCount detailing the amount
    *    of tokens for the specified place
    **/
    public function get($place) {
        if(!isset($this->vector[$place])) {
            return 0;
        }
        return $this->vector[$place];
    }

   /**
    * Create a new marking from this marking with certain places
    * marked as unbounded.
    * @param \Cora\Systems\Petrinet The petrinet for which to create
    *    the new marking
    * @param Set $places the set of places to be marked as unbounded
    * @return Marking The new marking
    **/
    public function markUnbounded($petrinet, $places) {
        $m = new Marking($petrinet, $this->vector);
        foreach($places as $i => $place) {
            $m->vector[$place] = new Tokens\OmegaTokenCount();
        }
        return $m;
    }

   /**
    * Get the set of all unbounded places for this marking
    * @return Set The set of unbounded places for this marking
    **/
    public function unbounded() {
        $res = new Set();
        foreach($this->vector as $place => $tokens) {
            if($tokens instanceof Tokens\OmegaTokenCount ) {
                $res->add($place);
            }
        }
        return $res;
    }

    /**
     * Does this marking cover another marking?
     *
     * @param Marking $other
     * @param Petrinet $petrinet
     * @return bool
     */
    public function covers($other, $petrinet)
    {
        $places = $petrinet->getPlaces();
        $covers = true;
        foreach($places as $i => $place) {
            $tokens = $this->get($place);
            if(!$tokens->geq($other->get($place))) {
                $covers = false;
                break;
            }
        }
        return $covers;
    }

    /**
     * Which places of another marking does this marking cover
     * Returns an empty set if this marking does not cover the other.
     *
     * @param Marking $other
     * @param Petrinet $petrinet
     * @return Set of places
     */
    public function covered($other, $petrinet)
    {
        $places = $petrinet->getPlaces();
        $res = new Set();
        if($this->covers($other, $petrinet)) {
            foreach($places as $i => $place) {
                $tokens = $this->get($place);
                if($tokens->greater($other->get($place))) {
                    $res->add($place);
                }
            }
        }
        return $res;
    }

    /**
     * Converts a list of the form place => tokens
     * to a full vector. Meaning that there is a
     * token count for each place.
     */
    protected function listToVector($places, $list=NULL)
    {
        $vector = [];
        // initialize all places to 0
        foreach($places as $i => $place)
        {
            $vector[$place] = Tokens\TokenCountFactory::getTokenCount(0);
        }
        // rewrite for each place that is defined
        if (!is_null($list)) {
            foreach($list as $place => $tokens)
            {
                $count = Tokens\TokenCountFactory::getTokenCount($tokens);
                $vector[$place] = $count;
            }
        }
        return $vector;
    }

    public function toArray() {
        return $this->vector;
    }

    // implement interfaces
    public function jsonSerialize()
    {
        return $this->vector;
    }

    public function rewind()
    {
        reset($this->vector);
    }
    
    public function current()
    {
        $cur = current($this->vector);
        return $cur;
    }

    public function key()
    {
        $key = key($this->vector);
        return $key;
    }

    public function next()
    {
        $next = next($this->vector);
        return $next;
    }

    public function valid()
    {
        $key = key($this->vector);
        return ($key !== NULL && $key !== FALSE);
    }

    public function __toString()
    {
        $vector = $this->vector;
        $s = implode(", ", $vector);
        return $s;
    }

    public function equals($other) : bool
    {
        return $this->hash() == $other->hash();
    }

    public function hash() 
    {
        return $this->vector;
    }
    /**
     * Converts a string to a marking vector
     * String have to be of the following format:
     * place1:number, place2:number, ...
     * @param string $string
     * @return Marking
     */
    public static function stringToMarking($petrinet, $string)
    {
        $string = preg_replace('/ /', '', $string);
        $pairs  = explode(",", $string);
        $arr = [];
        foreach($pairs as $i => $pair) {
            $els = explode(":", $pair);
            if(count($els) != 2) continue;
            $place = $els[0];
            $tokens = $els[1];
            $qualifier;
            if(is_numeric($tokens)) {
                $qualifier = intval($tokens);
            } else {
                $qualifier = "OMEGA";
            }
            $arr[$place] = $qualifier;
        }
        $class = static::class; //magic
        $marking = new $class($petrinet, $arr);
        return $marking;
    }
}

?>
