<?php

namespace Cora\Domain\Systems;

use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\Tokens\IntegerTokenCount as Tokens;
use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;

interface MarkingBuilderInterface {
    public function assign(Element $e, Tokens $t): void;
    public function getMarking(Petrinet $p): Marking;
}
