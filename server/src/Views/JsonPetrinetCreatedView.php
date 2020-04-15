<?php

namespace Cora\Views;

class JsonPetrinetCreatedView implements PetrinetCreatedViewInterface {
    use JsonViewTrait;

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
