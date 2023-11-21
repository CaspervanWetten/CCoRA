<?php

namespace Cora\Domain\Petrinet;

use JsonSerializable;

class PetrinetRecord implements JsonSerializable {
    protected $id;
    protected $name;

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function jsonSerialize(): mixed {
        return [
            "id" => $this->getId(),
            "name" => $this->getName()
        ];
    }
}
