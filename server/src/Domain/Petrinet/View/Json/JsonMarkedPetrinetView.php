<?php

namespace Cora\Domain\Petrinet\View\Json;

use Cora\Domain\Petrinet\Marking\MarkingInterface as Marking;
use Cora\Domain\Petrinet\View\MarkedPetrinetViewInterface;

class JsonMarkedPetrinetView
    extends JsonPetrinetView
    implements MarkedPetrinetViewInterface
{
    protected $marking;

    public function setMarking(?Marking $marking): void {
        $this->marking = $marking;
    }

    public function getMarking(): ?Marking {
        return $this->marking;
    }

    public function render(): string {
        $petrinet = $this->getPetrinet();
        $marking = $this->getMarking();
        return json_encode([
            "petrinet" => $petrinet,
            "marking" => $marking
        ]);
    }
}
