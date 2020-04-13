<?php

namespace Cora\Domain\Systems\Petrinet;

use Ds\Set;

interface PetrinetElementContainerInterface {
    public function isEmpty(): bool;
    public function toSet(): Set;
    public function toArray(): array;
}
