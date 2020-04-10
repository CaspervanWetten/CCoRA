<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\FlowInterface as Flow;

use IteratorAggregate;

interface FlowMapInterface extends IteratorAggregate {
    public function has(Flow $flow): bool;
    public function get(Flow $flow): int;
    public function add(Flow $flow, int $weight): void;
    public function create(PetrinetInterface $p): FlowMapInterface;
}
