<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;

interface PetrinetBuilderInterface {
    public function addPlace(Place $p): void;
    public function addTransition(Transition $t): void;
    public function addFlow(Flow $f, int $weight): void;
    public function getPetrinet(): Petrinet;

    public function hasPlace(string $place): bool;
    public function hasTransition(string $trans): bool;
}
