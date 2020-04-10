<?php

namespace Cora\Domain\Systems;

use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;
use Ds\Map;

class MarkingMap implements MarkingMapInterface {
    protected $map;

    public function __construct() {
        $this->map = new Map();
    }

    public function get(Element $e): Marking {
        return $this->map->get($e);
    }
    
    public function put(Element $e, Marking $m): void {
        $this->map->put($e, $m);
    }

    public function has(Element $e): bool {
        return $this->map->hasKey($e);
    }
}
