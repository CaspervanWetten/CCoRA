<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;

use IteratorAggregate;

interface PetrinetElementContainerInterface extends IteratorAggregate {
    public function add(Element $e): void;
    public function has(Element $e): bool;
}
