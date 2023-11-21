<?php

namespace Cora\Domain\Petrinet\Marking;

use Cora\Domain\Petrinet\Marking\MarkingInterface as Marking;
use Cora\Domain\Petrinet\Transition\Transition;
use Cora\Domain\Petrinet\Transition\TransitionContainer as Transitions;
use Cora\Domain\Petrinet\Transition\TransitionContainerInterface as ITransitions;

use Ds\Map;
use Traversable;

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

    public function getIterator(): Traversable {
        return $this->map->getIterator();
    }

    public function jsonSerialize(): mixed {
        return $this->map;
    }
}
