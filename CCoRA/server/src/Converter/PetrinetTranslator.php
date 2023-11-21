<?php

namespace Cora\Converter;

use Cora\Domain\Petrinet\PetrinetBuilder;
use Cora\Domain\Petrinet\MarkedPetrinetInterface as IMarkedPetrinet;
use Cora\Domain\Petrinet\MarkedPetrinet;
use Cora\Domain\Petrinet\Place\Place;
use Cora\Domain\Petrinet\Transition\Transition;
use Cora\Domain\Petrinet\Flow\Flow;
use Cora\Domain\Petrinet\Marking\MarkingBuilder;

use Ds\Map;
use Exception;

class PetrinetTranslator extends Converter {
    protected $markedPetrinet;
    protected $petrinetBuilder;
    protected $markingBuilder;

    protected $placeTranslations;
    protected $transitionTranslations;

    public function __construct(IMarkedPetrinet $net) {
        $this->markedPetrinet = $net;
        $this->petrinetBuilder = new PetrinetBuilder();
        $this->markingBuilder = new MarkingBuilder();
        $this->placeTranslations = new Map();
        $this->transitionTranslations = new Map();
    }

    public function convert() {
        $this->translatePlaces();
        $this->translateTransitions();
        $this->translateFlows();
        $newPetrinet = $this->petrinetBuilder->getPetrinet();
        $this->translateMarking();
        $newMarking = $this->markingBuilder->getMarking($newPetrinet);
        return new MarkedPetrinet($newPetrinet, $newMarking);
    }

    protected function translatePlaces() {
        $places = $this->markedPetrinet->getPetrinet()->getPlaces();
        foreach($places as $i => $place) {
            $newName = sprintf("p%d", $i + 1);
            $newPlace = new Place($newName);
            $this->placeTranslations->put($place, $newPlace);
            $this->petrinetBuilder->addPlace($newPlace);
        }
    }

    protected function translateTransitions() {
        $transitions = $this->markedPetrinet->getPetrinet()->getTransitions();
        foreach($transitions as $i => $trans) {
            $newName = sprintf("t%d", $i + 1);
            $newTransition = new Transition($newName);
            $this->transitionTranslations->put($trans, $newTransition);
            $this->petrinetBuilder->addTransition($newTransition);
        }
    }

    protected function translateFlows() {
        $flows = $this->markedPetrinet->getPetrinet()->getFlows();
        foreach($flows as $flow => $weight) {
            $from = $flow->getFrom();
            $to = $flow->getTo();
            $newFlow = NULL;
            if ($this->placeTranslations->hasKey($from) &&
                $this->transitionTranslations->hasKey($to))
                $newFlow = new Flow($this->placeTranslations->get($from),
                                    $this->transitionTranslations->get($to));
            elseif ($this->transitionTranslations->hasKey($from) &&
                    $this->placeTranslations->hasKey($to))
                $newFlow = new Flow($this->transitionTranslations->get($from),
                                    $this->placeTranslations->get($to));
            else
                throw new Exception("Could not translate flows: " .
                                    "misformed translations");
            $this->petrinetBuilder->addFlow($newFlow, $weight);
        }
    }

    protected function translateMarking() {
        $marking = $this->markedPetrinet->getMarking();
        foreach($marking as $place => $tokens) {
            $newPlace = $this->placeTranslations->get($place);
            $this->markingBuilder->assign($newPlace, $tokens);
        }
    }
}
