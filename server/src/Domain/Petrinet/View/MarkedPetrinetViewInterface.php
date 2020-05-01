<?php

namespace Cora\Domain\Petrinet\View;

use Cora\Domain\Petrinet\Marking\MarkingInterface as Marking;

interface MarkedPetrinetViewInterface extends PetrinetViewInterface {
    public function setMarking(?Marking $marking): void;
    public function getMarking(): ?Marking;
}
