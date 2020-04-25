<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Petrinet\Place\Place;
use Cora\Domain\Systems\Petrinet\Transition\Transition;
use Cora\Domain\Systems\Petrinet\Flow\Flow;
use Cora\Domain\Systems\Petrinet\Place\PlaceContainerInterface as Places;
use Cora\Domain\Systems\Petrinet\Transition\TransitionContainerInterface as Transitions;
use Cora\Domain\Systems\Petrinet\Flow\FlowMapInterface as FlowMap;

interface PetrinetBuilderInterface {
    public function addPlace(Place $p): void;
    public function addTransition(Transition $t): void;
    public function addFlow(Flow $f, int $weight): void;
    public function addPlaces(Places $places): void;
    public function addTransitions(Transitions $transitions): void;
    public function addFlows(FlowMap $flows): void;
    public function getPetrinet(): Petrinet;

    public function hasPlace(string $place): bool;
    public function hasTransition(string $trans): bool;
}
