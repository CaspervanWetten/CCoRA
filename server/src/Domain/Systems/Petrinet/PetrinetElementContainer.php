<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;

use Ds\Set;

class PetrinetElementContainer implements PetrinetElementContainerInterface {
    protected $set;

    public function __construct() {
        $this->set = new Set();
    }

    public function add(Element $e): void {
        $this->set->add($e);
    }

    public function has(Element $e): bool {
        return $this->set->contains($e);
    }

    public function getIterator() {
        return $this->set->getIterator();
    }
}
