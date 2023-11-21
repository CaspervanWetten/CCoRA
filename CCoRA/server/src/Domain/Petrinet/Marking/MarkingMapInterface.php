<?php

namespace Cora\Domain\Petrinet\Marking;

use Cora\Domain\Petrinet\Marking\MarkingInterface as Marking;
use Cora\Domain\Petrinet\Transition\Transition;
use Cora\Domain\Petrinet\Transition\TransitionContainerInterface as Transitions;

use IteratorAggregate;
use JsonSerializable;

interface MarkingMapInterface extends JsonSerializable, IteratorAggregate {
    public function get(Transition $t): Marking;
    public function put(Transition $t, Marking $m): void;
    public function has(Transition $t): bool;
    public function transitions(): Transitions;
}
