<?php

namespace Cora\Domain\Petrinet;

use Cora\Domain\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Petrinet\Marking\MarkingInterface as Marking;

class MarkedPetrinet implements MarkedPetrinetInterface {
    private $petrinet;
    private $marking;

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

    public function jsonSerialize(): mixed {
        return [
            "petrinet" => $this->getPetrinet(),
            "marking" => $this->getMarking()
        ];
    }
}
