<?php

namespace Cora\Domain\Systems\Petrinet;

class Place implements PetrinetElementInterface {
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
}
