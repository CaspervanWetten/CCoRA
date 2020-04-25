<?php

namespace Cora\Domain\Systems\Petrinet\Flow;

use Cora\Domain\Systems\Petrinet\Flow\FlowInterface as Flow;

use Ds\Set;

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

    public function getIterator() {
        return $this->set->getIterator();
    }

    public function jsonSerialize() {
        return $this->set;
    }
}
