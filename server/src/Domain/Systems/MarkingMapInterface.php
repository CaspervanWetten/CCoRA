<?php

namespace Cora\Domain\Systems;

use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\Petrinet\Transition;
use Cora\Domain\Systems\Petrinet\TransitionContainerInterface as Transitions;

use IteratorAggregate;
use JsonSerializable;

interface MarkingMapInterface extends JsonSerializable, IteratorAggregate {
    public function get(Transition $t): Marking;
    public function put(Transition $t, Marking $m): void;
    public function has(Transition $t): bool;
    public function transitions(): Transitions;
}
