<?php

namespace Cora\Domain\Petrinet\Marking;

use Cora\Domain\Petrinet\Marking\MarkingInterface as Marking;
use Cora\Domain\Petrinet\Marking\Tokens\IntegerTokenCount as Tokens;
use Cora\Domain\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Petrinet\Place\Place;

interface MarkingBuilderInterface {
    public function assign(Place $p, Tokens $t): void;
    public function getMarking(Petrinet $p): Marking;
}
