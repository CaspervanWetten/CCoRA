<?php

namespace Cora\Domain\Petrinet\Transition;

use Cora\Domain\Petrinet\PetrinetElementInterface;

use JsonSerializable;

class Transition implements PetrinetElementInterface, JsonSerializable {
    protected $id;
    protected $coordinates;
    protected $label;

    public function __construct(string $id, array $coordinates=[NULL,NULL], string $label=NULL) {
        $this->id = $id;
        $this->coordinates = $coordinates;
        $this->label = $label;
    }

    public function getID(): string {
        return $this->id;
    }

    public function getCoordinates(): array {
        return $this->coordinates;
    }

    public function getLabel(): ?string {
        return $this->label;
    }

    public function hash() {
        return $this->getID();
    }

    public function equals($obj): bool {
        return $this->getID() === $obj->getID();
    }

    public function jsonSerialize(): mixed {
        return $this->getID();
    }

    public function __toString() {
        return $this->getID();
    }
}
