<?php

namespace Cora\Views\Json;

use Cora\Views;

class JsonPetrinetCreatedView implements Views\PetrinetCreatedViewInterface {
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
