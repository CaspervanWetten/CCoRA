<?php

namespace Cora\Domain\Systems\Petrinet;

interface TransitionContainerInterface {
    public function contains(Transition $t): bool;
    public function add(Transition $t): void;
}
