<?php

namespace Cora\Views;

class JsonPetrinetsView implements PetrinetsViewInterface {
    use JsonViewTrait;

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
