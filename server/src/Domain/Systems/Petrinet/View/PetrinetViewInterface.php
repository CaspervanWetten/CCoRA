<?php

namespace Cora\Domain\Systems\Petrinet\View;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Views\ViewInterface;

interface PetrinetViewInterface extends ViewInterface {
    public function setPetrinet(IPetrinet $petrinet): void;
}
