<?php

namespace Cora\Domain\Petrinet\View;

use Cora\Domain\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Views\ViewInterface;

interface PetrinetViewInterface extends ViewInterface {
    public function setPetrinet(IPetrinet $petrinet): void;
}
