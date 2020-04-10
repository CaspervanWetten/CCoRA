<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\MarkingInterface;

interface MarkedPetrinetInterface {
    public function getPetrinet(): PetrinetInterface;
    public function getMarking(): MarkingInterface;
}
