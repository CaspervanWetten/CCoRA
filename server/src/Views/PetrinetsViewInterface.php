<?php

namespace Cora\Views;

interface PetrinetsViewInterface extends ViewInterface {
    public function getPetrinets(): array;
    public function setPetrinets(array $petrinets): void;
}
