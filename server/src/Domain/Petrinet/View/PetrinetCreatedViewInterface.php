<?php

namespace Cora\Domain\Petrinet\View;

use Cora\Domain\Petrinet\MarkedPetrinetRegisteredResult as Result;
use Cora\Views\ViewInterface;

interface PetrinetCreatedViewInterface extends ViewInterface {
    public function setResult(Result $result);
    public function getResult(): Result;
}
