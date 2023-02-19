<?php

namespace Cora\Domain\Petrinet\Place;

use Cora\Domain\Petrinet\PetrinetElementInterface;

use Ds\Hashable;
use JsonSerializable;

class Place implements PetrinetElementInterface, JsonSerializable, Hashable {
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
