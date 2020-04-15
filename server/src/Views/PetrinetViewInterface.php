<?php

namespace Cora\Views;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as IPetrinet;

interface PetrinetViewInterface extends ViewInterface {
    public function setPetrinet(IPetrinet $petrinet): void;
}
