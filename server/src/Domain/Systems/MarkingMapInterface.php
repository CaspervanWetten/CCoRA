<?php

namespace Cora\Domain\Systems;

use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;

interface MarkingMapInterface {
    public function get(Element $e): Marking;
    public function put(Element $e, Marking $m): void;
    public function has(Element $e): bool;
}
