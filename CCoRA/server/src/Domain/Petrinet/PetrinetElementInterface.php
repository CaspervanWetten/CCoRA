<?php

namespace Cora\Domain\Petrinet;

use Ds\Hashable;

interface PetrinetElementInterface extends Hashable {
    public function getID(): string;
}
