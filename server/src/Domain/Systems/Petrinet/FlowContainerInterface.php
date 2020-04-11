<?php

namespace Cora\Domain\Systems\Petrinet;

use IteratorAggregate;

interface FlowContainerInterface extends IteratorAggregate {
    public function contains(Flow $flow): bool;
    public function add(Flow $flow): void;
}
