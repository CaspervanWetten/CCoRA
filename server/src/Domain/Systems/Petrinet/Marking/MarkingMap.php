<?php

namespace Cora\Domain\Systems\Petrinet\Marking;

use Cora\Domain\Systems\Petrinet\Marking\MarkingInterface as Marking;
use Cora\Domain\Systems\Petrinet\Transition\Transition;
use Cora\Domain\Systems\Petrinet\Transition\TransitionContainer as Transitions;
use Cora\Domain\Systems\Petrinet\Transition\TransitionContainerInterface as ITransitions;

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

    public function transitions(): ITransitions {
        return new Transitions($this->map->keys());
    }

    public function getIterator() {
        return $this->map->getIterator();
    }

    public function jsonSerialize() {
        return $this->map;
    }
}
