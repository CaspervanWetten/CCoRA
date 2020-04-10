<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;
use Cora\Domain\Systems\MarkingInterface as Marking;

interface PetrinetBuilderInterface {
    public function addElement(Element $e): void;
    public function addFlow(Flow $f, int $weight): void;
    public function setInitial(Marking $m): void;
    public function getPetrinet(): Petrinet;
}
