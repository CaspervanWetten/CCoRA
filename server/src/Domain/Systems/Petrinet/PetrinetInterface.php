<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\MarkingMapInterface as MarkingMap;
use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;
use Cora\Domain\Systems\Petrinet\PlaceContainerInterface as PlaceContainer;
use Cora\Domain\Systems\Petrinet\TransitionContainerInterface as TransitionContainer;
use Cora\Domain\Systems\Petrinet\Transition;

interface PetrinetInterface {
    public function enabled(Marking $marking, Transition $e): bool;
    public function fire(Marking $marking, Transition $e): Marking;
    public function reachable(Marking $marking): MarkingMap;
    public function enabledTransitions(Marking $marking): TransitionContainer;
    public function preset(Element $e): PrePostSetMapInterface;
    public function postset(Element $e): PrePostSetMapInterface;

    public function getPlaces(): PlaceContainer;
    public function getTransitions(): TransitionContainer;
    public function getFlows(): FlowMapInterface;
}
