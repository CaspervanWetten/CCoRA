<?php

namespace Cora\Systems;

use \Ds\Set as Set;

class Marking implements \JsonSerializable, \Iterator, \Ds\Hashable
{
    public $vector;
    public $reachable;

    public function __construct($petrinet, $list = [], $reachable=false)
    {
        $this->vector = $this->listToVector($petrinet->getPlaces(), $list);
        $this->reachable = $reachable;
    }

    public function get($place)
    {
        if(!isset($this->vector[$place])) {
            return 0;
        }
        return $this->vector[$place];
    }
    
    public function markUnbounded($petrinet, $places)
    {
        $m = new Marking($petrinet, $this->vector);
        foreach($places as $i => $place) {
            $m->vector[$place] = new \Cora\Systems\OmegaTokenCount();
        }
        return $m;
    }

    public function unbounded()
    {
        $res = new Set();
        foreach($this->vector as $place => $tokens) {
            if($tokens instanceof OmegaTokenCount ) {
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
    protected function listToVector($places, $list)
    {
        $vector = [];
        // initialize all places to 0
        foreach($places as $i => $place)
        {
            $vector[$place] = TokenCountFactory::getTokenCount(0);
        }
        // rewrite for each place that is defined
        foreach($list as $place => $tokens)
        {
            $count = \Cora\Systems\TokenCountFactory::getTokenCount($tokens);
            $vector[$place] = $count;
        }
        return $vector;
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
