<?php

namespace Cora\Domain\Systems\Petrinet;

interface PetrinetElementInterface {
    public function getName(): string;
    public function getType(): PetrinetElementType;
}
