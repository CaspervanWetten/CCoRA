<?php

namespace Cora\Domain\Systems;

use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\Petrinet\Transition;
use Cora\Domain\Systems\Petrinet\TransitionContainer as Transitions;

use Ds\Map;

class MarkingMap implements MarkingMapInterface {
    protected $map;

    public function __construct() {
        $this->map = new Map();
    }

    public function get(Transition $t): Marking {
        return $this->map->get($t);
    }
    
    public function put(Transition $t, Marking $m): void {
        $this->map->put($t, $m);
    }

    public function has(Transition $t): bool {
        return $this->map->hasKey($t);
    }

    public function transitions(): Transitions {
        return new Transitions($this->map->keys());
    }

    public function jsonSerialize() {
        return $this->map;
    }
}
