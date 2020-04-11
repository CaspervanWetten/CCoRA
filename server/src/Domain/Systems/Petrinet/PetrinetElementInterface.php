<?php

namespace Cora\Domain\Systems\Petrinet;

use Ds\Hashable;

interface PetrinetElementInterface extends Hashable {
    public function getName(): string;
}
