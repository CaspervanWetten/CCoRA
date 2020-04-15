<?php

namespace Cora\Views;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as IPetrinet;

class JsonPetrinetView implements PetrinetViewInterface {
    use JsonViewTrait;

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
