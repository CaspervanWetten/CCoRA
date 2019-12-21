<?php

namespace Cora\Converters;

use \Cora\Systems\Petrinet as Petrinet;

class PetrinetToJson extends Converter {
    protected $petrinet;

    public function __construct($petrinet) {
        $this->petrinet = $petrinet;
    }

    public function convert() {
        $p = $this->petrinet;

        $places = $p->getPlaces();
        $transitions = $p-> getTransitions();
        $flows = $p->getFlows();
        $initMarking = $p->getInitial();

        $f = [];
        foreach($flows as $pair => $weight) {
            array_push($f, [
                "from"   => $pair->from,
                "to"     => $pair->to,
                "weight" => $weight
            ]);
        }
        return [
            "places"         => $places,
            "transitions"    => $transitions,
            "flows"          => $f,
            "initialMarking" => $initMarking
        ];
    }
}
?>
