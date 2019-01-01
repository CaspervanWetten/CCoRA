<?php

namespace Cora\Converters;

use \Ds\Map;

use \Cora\Systems\Petrinet as Petrinet;

class PetrinetTranslator extends Converter
{
    protected $petrinet;                    // the petrinet to translate
    protected $placeTranslationTable;       // contains place translations
    protected $transitionTranslationTable;  // contains transition translations

    public function __construct($petrinet)
    {
        $this->petrinet = $petrinet;
        $this->placeTranslationTable = [];
        $this->transitionTranslationTable = [];
    }

    public function convert()
    {
        $places      = $this->convertPlaces();
        $transitions = $this->convertTransitions();
        $flows       = $this->convertFlows();
        $marking     = $this->convertMarking();

        $this->petrinet->setPlaces($places);
        $this->petrinet->setTransitions($transitions);
        $this->petrinet->setFlows($flows);
        $this->petrinet->setInitialMarking($marking);

        return $this->petrinet;
    }

    protected function convertPlaces()
    {
        $places = $this->petrinet->getPlaces();
        $names = [];
        foreach($places as $i => $place) {
            $p = $place;
            if(array_key_exists($place, $this->placeTranslationTable)) {
                $p = $this->placeTranslationTable[$place];
            }
            else {
                $p = sprintf("p%d", count($this->placeTranslationTable) + 1);
                $this->placeTranslationTable[$place] = $p;
            }
            array_push($names, $p);
        }
        return $names;
    }

    protected function convertTransitions()
    {
        $transitions = $this->petrinet->getTransitions();
        $names = [];
        foreach($transitions as $i => $transition) {
            $t = $transition;
            if(array_key_exists($transition, $this->transitionTranslationTable)) {
                $t = $this->transitionTranslationTable[$transition];
            }
            else {
                $t = sprintf("t%d", count($this->transitionTranslationTable) + 1);
                $this->transitionTranslationTable[$transition] = $t;
            }
            array_push($names, $t);
        }
        return $names;   
    }

    protected function convertFlows()
    {
        $flows = $this->petrinet->getFlows();
        $result = new Map();
        foreach($flows as $flow => $weight){
            $from = $flow->from;
            $to   = $flow->to;
            
            if(array_key_exists($from, $this->placeTranslationTable)) {
                $from = $this->placeTranslationTable[$from];
            }
            elseif(array_key_exists($from, $this->transitionTranslationTable)) {
                $from = $this->transitionTranslationTable[$from];
            }
            // translate to
            if(array_key_exists($to, $this->placeTranslationTable)) {
                $to = $this->placeTranslationTable[$to];
            }
            elseif(array_key_exists($to, $this->transitionTranslationTable)) {
                $to = $this->transitionTranslationTable[$to];
            }

            $f = new Petrinet\Flow($from, $to);
            $result->put($f, $weight);
        }
        return $result;
    }

    protected function convertMarking()
    {
        $marking = $this->petrinet->getInitialMarking();
        $result = [];
        foreach($marking as $place => $tokens) {
            $p = $place;
            if(array_key_exists($place, $this->placeTranslationTable)) {
                $p = $this->placeTranslationTable[$place];
            }
            $result[$p] = $tokens;
        }
        return $result;
    }
}

?>
