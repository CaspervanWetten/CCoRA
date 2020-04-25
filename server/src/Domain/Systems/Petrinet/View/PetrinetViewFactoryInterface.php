<?php

namespace Cora\Domain\Systems\Petrinet\View;

interface PetrinetViewFactoryInterface {
    public function createPetrinetView(): PetrinetViewInterface;
    public function createPetrinetsView(): PetrinetsViewInterface;
    public function createPetrinetCreatedView(): PetrinetCreatedViewInterface;
}
