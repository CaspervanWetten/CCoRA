<?php

namespace Cora\Domain\Systems;

use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\Tokens\TokenCountInterface as Tokens;
use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Petrinet\Place;

use Ds\Map;
use Exception;

class MarkingBuilder implements MarkingBuilderInterface {
    protected $map;
    
    public function __construct() {
        $this->map = new Map();
    }

    public function assign(Place $p, Tokens $t): void {
        $this->map->put($p, $t);
    }

    public function getMarking(Petrinet $net): Marking {
        $places = $net->getPlaces();
        $assignedPlaces = $this->map->keys();
        foreach($assignedPlaces as $place) {
            if (!$places->contains($place))
                throw new Exception("Tokens assigned to invalid place");
        }
        $marking = new Marking2($this->map);
        return $marking;
    }
}
