<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Petrinet\PlaceContainerInterface as PlaceContainer;
use Cora\Domain\Systems\Petrinet\TransitionContainerInterface as TransitionContainer;
use Cora\Domain\Systems\Petrinet\FlowMapInterface as FlowMap;

interface PetrinetBuilderInterface {
    public function addPlace(Place $p): void;
    public function addTransition(Transition $t): void;
    public function addFlow(Flow $f, int $weight): void;
    public function addPlaces(PlaceContainer $places): void;
    public function addTransitions(TransitionContainer $transitions): void;
    public function addFlows(FlowMap $flows): void;
    public function getPetrinet(): Petrinet;

    public function hasPlace(string $place): bool;
    public function hasTransition(string $trans): bool;
}
