<?php

namespace Cora\Domain\Petrinet;

use Cora\Domain\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Domain\Petrinet\Place\Place;
use Cora\Domain\Petrinet\Place\PlaceContainer;
use Cora\Domain\Petrinet\Place\PlaceContainerInterface;
use Cora\Domain\Petrinet\Transition\Transition;
use Cora\Domain\Petrinet\Transition\TransitionContainer;
use Cora\Domain\Petrinet\Transition\TransitionContainerInterface;
use Cora\Domain\Petrinet\Flow\FlowInterface as IFlow;
use Cora\Domain\Petrinet\Flow\FlowMap;
use Cora\Domain\Petrinet\Flow\FlowMapInterface;

use Cora\Exception\BadInputException;

class PetrinetBuilder implements PetrinetBuilderInterface {
    protected $places;
    protected $transitions;
    protected $flows;

    public function __construct() {
        $this->places = new PlaceContainer();
        $this->transitions = new TransitionContainer();
        $this->flows = new FlowMap();
    }

    public function addPlace(Place $p): void {
        $this->places->add($p);
    }

    public function addTransition(Transition $t): void {
        $this->transitions->add($t);
    }

    public function addFlow(IFlow $f, int $w): void {
        $this->flows->add($f, $w);
    }

    public function addPlaces(PlaceContainerInterface $places): void {
        foreach($places as $place)
            $this->addPlace($place);
    }

    public function addTransitions(TransitionContainerInterface $transitions): void {
        foreach($transitions as $transition)
            $this->addTransition($transition);
    }

    public function addFlows(FlowMapInterface $flows): void {
        foreach($flows as $flow => $weight)
            $this->addFlow($flow, $weight);
    }

    public function getPetrinet(): IPetrinet {
        $flowMap = $this->flows;
        $places = $this->places;
        $transitions = $this->transitions;
        foreach($flowMap->flows() as $flow) {
            $from = $flow->getFrom();
            $to = $flow->getTo();
            if (!($from instanceof Place && $to instanceof Transition &&
                  $this->places->contains($from) && $this->transitions->contains($to)) &&
                !($from instanceof Transition && $to instanceof Place &&
                  $this->transitions->contains($from) && $this->places->contains($to)))
                throw new BadInputException(
                    "At least one flow has an element that has not been " .
                    "added to the transitions or places"
                );
        }
        $petrinet = new Petrinet($places, $transitions, $flowMap);
        return $petrinet;
    }

    public function hasPlace(string $place): bool {
        $place = new Place($place);
        return $this->places->contains($place);
    }

    public function hasTransition(string $transition): bool {
        $trans = new Transition($transition);
        return $this->transitions->contains($trans);
    }
}
