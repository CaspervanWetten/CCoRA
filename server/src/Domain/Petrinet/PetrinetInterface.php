<?php

namespace Cora\Domain\Petrinet;

use Cora\Domain\Petrinet\PetrinetElementInterface as Element;
use Cora\Domain\Petrinet\Place\PlaceContainerInterface as Places;
use Cora\Domain\Petrinet\Transition\Transition;
use Cora\Domain\Petrinet\Transition\TransitionContainerInterface as Transitions;
use Cora\Domain\Petrinet\Flow\FlowMapInterface;

use Cora\Domain\Petrinet\Marking\MarkingInterface as Marking;
use Cora\Domain\Petrinet\Marking\MarkingMapInterface as MarkingMap;

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
