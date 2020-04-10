<?php

namespace Cora\Domain\Systems\Petrinet;

use Ds\Hashable;

class PetrinetElement implements PetrinetElementInterface, Hashable {
    protected $name;
    protected $type;

    public function __construct(string $name, int $type) {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): int {
        return $this->type;
    }

    public function hash() {
        return $this->getName() . strval($this->getType());
    }

    public function equals($other): bool {
        return $this->getName() == $other->getName()
            && $this->getType() == $other->getType();
    }
}
