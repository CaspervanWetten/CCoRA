<?php

namespace Cora\Domain\Petrinet\Flow;

use Cora\Domain\Petrinet\Flow\FlowInterface as Flow;

use Ds\Set;
use Traversable;

class FlowContainer implements FlowContainerInterface {
    protected $set;

    public function __construct($from=[]) {
        $this->set = new Set($from);
    }

    public function contains(Flow $flow): bool {
        return $this->set->contains($flow);
    }

    public function add(Flow $flow): void {
        $this->set->add($flow);
    }

    public function getIterator(): Traversable {
        return $this->set->getIterator();
    }

    public function jsonSerialize(): mixed {
        return $this->set;
    }
}
