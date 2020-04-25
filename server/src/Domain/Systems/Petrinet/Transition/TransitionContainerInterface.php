<?php

namespace Cora\Domain\Systems\Petrinet\Transition;

use Cora\Domain\Systems\Petrinet\PetrinetElementContainerInterface;

use IteratorAggregate;
use JsonSerializable;

interface TransitionContainerInterface extends
    PetrinetElementContainerInterface,
    JsonSerializable,
    IteratorAggregate
{
    public function contains(Transition $t): bool;
    public function add(Transition $t): void;
    public function toArray(): array;
}
