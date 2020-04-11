<?php

namespace Cora\Domain\Systems\Petrinet;

use JsonSerializable;

interface TransitionContainerInterface extends JsonSerializable {
    public function contains(Transition $t): bool;
    public function add(Transition $t): void;
}
