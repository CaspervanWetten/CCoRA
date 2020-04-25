<?php

namespace Cora\Domain\Systems\Petrinet\Marking;

use Cora\Domain\Systems\Petrinet\Marking\MarkingInterface as IMarking;
use Cora\Domain\Systems\Petrinet\Marking\Tokens\TokenCountInterface as Tokens;
use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Petrinet\Place\Place;

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

    public function getMarking(Petrinet $net): IMarking {
        $places = $net->getPlaces();
        $assignedPlaces = $this->map->keys();
        foreach($assignedPlaces as $place) {
            if (!$places->contains($place))
                throw new Exception("Tokens assigned to invalid place");
        }
        $marking = new Marking($this->map);
        return $marking;
    }
}
