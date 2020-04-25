<?php

namespace Cora\Domain\Systems\Petrinet\Transition;

use Cora\Domain\Systems\Petrinet\PetrinetElementInterface;

use JsonSerializable;

class Transition implements PetrinetElementInterface, JsonSerializable {
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

    public function __toString() {
        return $this->getName();
    }
}
