<?php

namespace Cora\Domain\Systems\Petrinet\View;

use Cora\Views\ViewInterface;

interface PetrinetsViewInterface extends ViewInterface {
    public function getPetrinets(): array;
    public function setPetrinets(array $petrinets): void;
}
