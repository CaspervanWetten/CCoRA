<?php

namespace Cora\Domain\Systems\Petrinet\Flow;

use Cora\Domain\Systems\Petrinet\Flow\FlowInterface as Flow;
use Cora\Domain\Systems\Petrinet\Flow\FlowContainerInterface as Flows;

use IteratorAggregate;
use JsonSerializable;

interface FlowMapInterface extends IteratorAggregate, JsonSerializable {
    public function has(Flow $flow): bool;
    public function get(Flow $flow): int;
    public function add(Flow $flow, int $weight): void;
    public function flows(): Flows;
}
