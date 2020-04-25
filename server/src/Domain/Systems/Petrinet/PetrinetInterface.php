<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;
use Cora\Domain\Systems\Petrinet\Place\PlaceContainerInterface as Places;
use Cora\Domain\Systems\Petrinet\Transition\Transition;
use Cora\Domain\Systems\Petrinet\Transition\TransitionContainerInterface as Transitions;
use Cora\Domain\Systems\Petrinet\Flow\FlowMapInterface;

use Cora\Domain\Systems\Petrinet\Marking\MarkingInterface as Marking;
use Cora\Domain\Systems\Petrinet\Marking\MarkingMapInterface as MarkingMap;

use JsonSerializable;

interface PetrinetInterface extends JsonSerializable {
    public function enabled(Marking $marking, Transition $e): bool;
    public function fire(Marking $marking, Transition $e): Marking;
    public function reachable(Marking $marking): MarkingMap;
    public function enabledTransitions(Marking $marking): Transitions;
    public function preset(Element $e): PrePostSetMapInterface;
    public function postset(Element $e): PrePostSetMapInterface;

    public function getPlaces(): Places;
    public function getTransitions(): Transitions;
    public function getFlows(): FlowMapInterface;
}
