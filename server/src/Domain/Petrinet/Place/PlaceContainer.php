<?php

namespace Cora\Domain\Petrinet\Place;

use Ds\Set;
use Traversable;

class PlaceContainer implements PlaceContainerInterface {
    protected $set;

    public function __construct() {
        $this->set = new Set();
    }

    public function contains(Place $p): bool {
        return $this->set->contains($p);
    }

    public function add(Place $p): void {
        $this->set->add($p);
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
