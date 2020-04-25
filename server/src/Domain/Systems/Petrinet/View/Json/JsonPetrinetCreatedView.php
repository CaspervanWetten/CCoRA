<?php

namespace Cora\Domain\Systems\Petrinet\View\Json;

use Cora\Domain\Systems\Petrinet\View\PetrinetCreatedViewInterface;

class JsonPetrinetCreatedView implements PetrinetCreatedViewInterface {
    protected $id;

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function render(): string {
        return json_encode([
            "petrinet_id" => $this->getId()
        ]);
    }
}
