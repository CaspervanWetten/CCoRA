<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\MarkingInterface;

use JsonSerializable;

interface MarkedPetrinetInterface extends JsonSerializable {
    public function getPetrinet(): PetrinetInterface;
    public function getMarking(): MarkingInterface;
}
