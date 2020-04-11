<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\MarkingInterface as Marking;

class MarkedPetrinet implements MarkedPetrinetInterface {
    public function __construct(Petrinet $p, Marking $m) {
        $this->petrinet = $p;
        $this->marking = $m;
    }

    public function getPetrinet(): Petrinet {
        return $this->petrinet;
    }

    public function getMarking(): Marking {
        return $this->marking;
    }

    public function jsonSerialize() {
        return [
            "petrinet" => $this->getPetrinet(),
            "marking" => $this->getMarking()
        ];
    }
}
