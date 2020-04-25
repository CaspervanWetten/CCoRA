<?php

namespace Cora\Domain\Systems\Petrinet\View\Json;

use Cora\Domain\Systems\Petrinet\View\PetrinetsViewInterface;

class JsonPetrinetsView implements PetrinetsViewInterface {
    protected $petrinets;

    public function getPetrinets(): array {
        return $this->petrinets;
    }

    public function setPetrinets(array $petrinets): void {
        $this->petrinets = $petrinets;
    }

    public function render(): string {
        return json_encode($this->getPetrinets());
    }
}
