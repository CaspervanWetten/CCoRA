<?php

namespace Cora\Domain\Petrinet\Marking;

use Cora\Domain\Petrinet\Marking\MarkingInterface as IMarking;
use Cora\Domain\Petrinet\Marking\Tokens\TokenCountInterface as Tokens;
use Cora\Domain\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Petrinet\Place\Place;
use Cora\Exception\BadInputException;

use Ds\Map;

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
                throw new BadInputException("Tokens assigned to invalid place");
        }
        $marking = new Marking($this->map);
        return $marking;
    }
}
