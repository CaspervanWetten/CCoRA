<?php

namespace Cora\Domain\Petrinet;

use Cora\Domain\Petrinet\Marking\MarkingInterface;

use JsonSerializable;

interface MarkedPetrinetInterface extends JsonSerializable {
    public function getPetrinet(): PetrinetInterface;
    public function getMarking(): MarkingInterface;
}
