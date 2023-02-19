<?php

namespace Cora\View\Json;

use Cora\View\PetrinetsViewInterface;

class PetrinetsView implements PetrinetsViewInterface {
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
