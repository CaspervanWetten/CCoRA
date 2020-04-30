<?php

namespace Cora\Domain\Petrinet\View\Json;

use Cora\Domain\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Domain\Petrinet\View\PetrinetViewInterface;

class JsonPetrinetView implements PetrinetViewInterface {
    protected $petrinet;

    public function setPetrinet(IPetrinet $petrinet): void {
        $this->petrinet = $petrinet;
    }

    public function getPetrinet(): IPetrinet {
        return $this->petrinet;
    }

    public function render(): string {
        return json_encode($this->getPetrinet());
    }
}
