<?php

namespace Cora\Domain\Systems;

use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\Tokens\IntegerTokenCount as Tokens;
use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;
use Cora\Domain\Systems\Petrinet\PetrinetElementType as ElementType;

use Ds\Map;
use Exception;

class MarkingBuilder implements MarkingInterface {
    protected $map;
    
    public function __construct() {
        $this->map = new Map();
    }

    public function assign(Element $e, Tokens $t): void {
        if ($e->getType() == ElementType::TRANSITION)
            throw new Exception("Cannot assign tokens to a transition");
        $this->map->put($e, $t);
    }

    public function getMarking(Petrinet $net): Marking {
        $places = $net->getPlaces();
        $assignedPlaces = $this->map->keys();
        foreach($assignedPlaces as $place) {
            if (!$places->has($place))
                throw new Exception("Tokens assigned to invalid place");
        }
        $marking = new Marking2($this->map);
        return $marking;
    }
}
