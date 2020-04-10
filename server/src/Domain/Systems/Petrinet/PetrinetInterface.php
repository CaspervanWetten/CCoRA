<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\MarkingMapInterface as MarkingMap;
use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;
use Cora\Domain\Systems\Petrinet\PetrinetElementContainerInterface as ElementC;


interface PetrinetInterface {
    public function enabled(Marking $marking, Element $e): bool;
    public function fire(Marking $marking, Element $e): Marking;
    public function reachable(Marking $marking): MarkingMap;
    public function enabledTransitions(Marking $marking): ElementC;
    public function preset(Element $e): ElementC;
    public function postset(Element $e): ElementC;

    public function getPlaces(): ElementC;
    public function getTransitions(): ElementC;
    public function getFlows(): FlowMapInterface;
}
