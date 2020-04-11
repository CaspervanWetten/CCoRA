<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\FlowInterface as Flow;
use Cora\Domain\Systems\Petrinet\FlowContainerInterface as Flows;

use IteratorAggregate;

interface FlowMapInterface extends IteratorAggregate {
    public function has(Flow $flow): bool;
    public function get(Flow $flow): int;
    public function add(Flow $flow, int $weight): void;
    public function flows(): Flows;
}
