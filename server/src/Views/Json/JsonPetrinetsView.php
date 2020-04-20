<?php

namespace Cora\Views\Json;

use Cora\Views;

class JsonPetrinetsView implements Views\PetrinetsViewInterface {
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
