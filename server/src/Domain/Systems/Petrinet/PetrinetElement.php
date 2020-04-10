<?php

namespace Cora\Domain\Systems\Petrinet;

class PetrinetElement implements PetrinetElementInterface {
    protected $name;
    protected $type;

    public function __construct(string $name, PetrinetElementType $type) {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): PetrinetElementType {
        return $this->type;
    }
}

