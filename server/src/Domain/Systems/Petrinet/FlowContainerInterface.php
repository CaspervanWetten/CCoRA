<?php

namespace Cora\Domain\Systems\Petrinet;

use IteratorAggregate;
use JsonSerializable;

interface FlowContainerInterface extends IteratorAggregate, JsonSerializable {
    public function contains(Flow $flow): bool;
    public function add(Flow $flow): void;
}
