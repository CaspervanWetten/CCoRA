<?php

namespace Cora\Domain\Petrinet\View;

use Cora\Views\ViewInterface;

interface PetrinetsViewInterface extends ViewInterface {
    public function getPetrinets(): array;
    public function setPetrinets(array $petrinets): void;
}
