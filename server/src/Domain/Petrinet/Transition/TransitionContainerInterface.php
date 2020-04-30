<?php

namespace Cora\Domain\Petrinet\Transition;

use Cora\Domain\Petrinet\PetrinetElementContainerInterface;

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
