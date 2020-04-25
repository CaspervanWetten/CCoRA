<?php

namespace Cora\Domain\Systems\Petrinet\Marking;

use Cora\Domain\Systems\Petrinet\Marking\MarkingInterface as Marking;
use Cora\Domain\Systems\Petrinet\Marking\Tokens\IntegerTokenCount as Tokens;
use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Petrinet\Place\Place;

interface MarkingBuilderInterface {
    public function assign(Place $p, Tokens $t): void;
    public function getMarking(Petrinet $p): Marking;
}
