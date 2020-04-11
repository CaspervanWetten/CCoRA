<?php

namespace Cora\Converters;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;

class Petrinet2ToJson extends Converter {
    protected $petrinet;

    public function __construct(Petrinet $net) {
        $this->petrinet = $net;
    }

    public function convert() {
        $p = $this->petrinet;
        $places      = $p->getPlaces();
        $transitions = $p->getTransitions();
        $flowMap     = $p->getFlows();

        return json_encode([
            "places"      => $places,
            "transitions" => $transitions,
            "flows"       => $flowMap
        ]);
    }
}
