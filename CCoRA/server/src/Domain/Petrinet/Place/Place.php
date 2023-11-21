<?php

namespace Cora\Domain\Petrinet\Place;

use Cora\Domain\Petrinet\PetrinetElementInterface;

use Ds\Hashable;
use JsonSerializable;

class Place implements PetrinetElementInterface, JsonSerializable, Hashable {
    public $id;
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

    public function getArray(): array {
        return array($this->getID(), $this->getCoordinates(), $this->getLabel());
    }

    public function jsonSerialize(): mixed {
        return $this->getID();
    }

    public function __toString() {
        return $this->getID();
    }
}
