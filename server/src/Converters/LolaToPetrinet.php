<?php

namespace Cora\Converters;

use \Cora\Domain\Systems\Petrinet\Flow;
use \Cora\Domain\Systems\Petrinet\Petrinet;

use \Ds\Set as Set;
use \Ds\Map as Map;

class LolaToPetrinet extends Converter
{
    protected $file; // the .Lola file to covert

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function convert()
    {
        $contents = file($this->file, FILE_IGNORE_NEW_LINES);
        
        $places = new Set();
        $transitions = new Set();
        $flows = new Map();
        $marking = null;

        foreach ($contents as $i => $line) {
            if(preg_match('/PLACE/i', $line)) {
                $places = $places->union($this->parseLolaPlaces($line));
            }

            if(preg_match('/MARKING/i', $line)) {
                $marking = $this->parseLolaMarking($line);
            }

            if(preg_match('/TRANSITION/i', $line)) {
                $lines = [$line, $contents[++$i], $contents[++$i]];
                $res = $this->parseLolaTransition($lines);

                $transitions->add($res["transition"]);
                $flows = $flows->union($res["flows"]);
            }
        }
        $p = new Petrinet($places, $transitions, $flows, $marking);
        return $p;
    }

    /**
     * Return a set of places found on a given line
     * following the .lola syntax for a place list
     *
     * @param string $line
     * @return \Ds\Set
     */
    protected function parseLolaPlaces($line)
    {
        $places = array();
        $s = trim(preg_replace('/PLACE|[,;]/i', '', $line));
        $s = explode(' ', $s);
        foreach ($s as $j => $p) {
            array_push($places, $p);
        }
        return new Set($places);
    }

    /**
     * Return a list of place:tokens pairs on a given line following
     * the .lola syntax.
     *
     * @param string $line
     * @return \Ds\Map
     */
    protected static function parseLolaMarking($line)
    {
        $m = trim(preg_replace('/MARKING|[ ;]/i', '', $line));
        $m = explode(',', $m);
        $l = array();
        foreach ($m as $j => $r) {
            list($t, $a) = sscanf($r, "%[^:]:%d");
            $l[$t] = $a;
        }
        return $l;
    }

    /**
     * Return the transition and its flows given 3 lines
     * following the .lola syntax
     *
     * @param [string] $lines
     * @return void
     */
    protected static function parseLolaTransition($lines)
    {
        $res = array();
        $flows = new Map();
        // line 1
        $line = $lines[0];
        list($transition) = sscanf($line, "TRANSITION %s");
        // line 2
        $line = $lines[1];
        $c = trim(preg_replace('/CONSUME|[ ;]/i', '', $line));
        $c = preg_split("/,/", $c, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($c as $j => $cons) {
            list($from, $amount) = sscanf($cons, "%[^:]:%d");
            $flow = new Flow($from, $transition);
            $flows->put($flow, intval($amount));
        }
        // line 3
        $line = $lines[2];
        $p = trim(preg_replace('/PRODUCE|[ ;]/i', '', $line));
        $p = preg_split("/,/", $p, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($p as $j => $pros) {
            list($to, $amount) = sscanf($pros, "%[^:]:%d");
            $flow = new Flow($transition, $to);
            $flows->put($flow, intval($amount));
        }
        $res["transition"] = $transition;
        $res["flows"] = $flows;
        return $res;
    }
}
?>
