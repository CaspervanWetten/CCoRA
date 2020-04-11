<?php

namespace Cora\Domain\Systems\Petrinet;

use JsonSerializable;

class Place implements PetrinetElementInterface, JsonSerializable {
    protected $name;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }

    public function hash() {
        return $this->getName();
    }

    public function equals($obj): bool {
        return $this->getName() === $obj->getName();
    }

    public function jsonSerialize() {
        return $this->getName();
    }
}
