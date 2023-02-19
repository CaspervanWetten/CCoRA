<?php

namespace Cora\Domain\Petrinet\Transition;

use Cora\Domain\Petrinet\PetrinetElementInterface;

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

    public function jsonSerialize(): mixed {
        return $this->getName();
    }

    public function __toString() {
        return $this->getName();
    }
}
