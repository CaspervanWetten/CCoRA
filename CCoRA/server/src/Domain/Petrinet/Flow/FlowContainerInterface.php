<?php

namespace Cora\Domain\Petrinet\Flow;

use IteratorAggregate;
use JsonSerializable;

interface FlowContainerInterface extends IteratorAggregate, JsonSerializable {
    public function contains(Flow $flow): bool;
    public function add(Flow $flow): void;
}
