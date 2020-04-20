<?php

namespace Cora\Views\Json;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Views;

class JsonPetrinetView implements Views\PetrinetViewInterface {
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
