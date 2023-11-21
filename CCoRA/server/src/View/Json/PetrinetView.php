<?php

namespace Cora\View\Json;

use Cora\View\PetrinetViewInterface;
use Cora\Domain\Petrinet\MarkedPetrinetInterface as IMarkedPetrinet;

class PetrinetView implements PetrinetViewInterface {
    protected $petrinet;

    public function setPetrinet(IMarkedPetrinet $petrinet): void {
        $this->petrinet = $petrinet;
    }

    public function getPetrinet(): IMarkedPetrinet {
        return $this->petrinet;
    }

    public function render(): string {
        return json_encode($this->getPetrinet());
    }
}
