<?php

namespace Cora\Domain\Petrinet\Transition;

use Ds\Set;
use Traversable;

class TransitionContainer implements TransitionContainerInterface {
    protected $set;

    public function __construct($values=null) {
        if ($values instanceof Traversable)
            $this->set = new Set($values);
        else
            $this->set = new Set();
    }

    public function contains(Transition $t): bool {
        return $this->set->contains($t);
    }

    public function add(Transition $t): void {
        $this->set->add($t);
    }

    public function isEmpty(): bool {
        return $this->set->isEmpty();
    }

    public function jsonSerialize(): mixed {
        return $this->set;
    }

    public function getIterator(): Traversable {
        return $this->set->getIterator();
    }

    public function toSet(): Set {
        return $this->set;
    }

    public function toArray(): array {
        return $this->set->toArray();
    }
}
