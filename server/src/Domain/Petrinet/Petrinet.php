<?php

namespace Cora\Domain\Petrinet;

use Cora\Domain\Petrinet\PetrinetElementInterface as IElement;
use Cora\Domain\Petrinet\Place\PlaceContainerInterface as IPlaces;
use Cora\Domain\Petrinet\Transition\Transition;
use Cora\Domain\Petrinet\Transition\TransitionContainer;
use Cora\Domain\Petrinet\Transition\TransitionContainerInterface as ITransitions;
use Cora\Domain\Petrinet\Flow\FlowMapInterface as IFlows;

use Cora\Domain\Petrinet\Marking\MarkingInterface as IMarking;
use Cora\Domain\Petrinet\Marking\MarkingMapInterface as IMarkingMap;
use Cora\Domain\Petrinet\Marking\MarkingMap;
use Cora\Domain\Petrinet\Marking\MarkingBuilder;
use Cora\Domain\Petrinet\Marking\Tokens\IntegerTokenCount;

class Petrinet implements PetrinetInterface {
    protected $places;
    protected $transitions;
    protected $flows;

    public function __construct (
        IPlaces $p,
        ITransitions $t,
        IFlows $f)
    {
        $this->places = $p;
        $this->transitions = $t;
        $this->flows = $f;
    }

    public function enabled(IMarking $marking, Transition $t): bool {
        $preset = $this->preset($t);
        foreach($preset as $place => $weight) {
            $tokens = $marking->get($place);
            if ($tokens instanceof IntegerTokenCount &&
                $tokens->getValue() < $weight)
                return false;
        }
        return true;
    }

    public function fire(IMarking $marking, Transition $t): IMarking {
        $pre = $this->preset($t);
        $post = $this->postset($t);

        $builder = new MarkingBuilder();
        foreach($this->places as $place) {
            $preWeight = new IntegerTokenCount($pre->get($place));
            $postWeight = new IntegerTokenCount($post->get($place));
            $tokens = $marking->get($place);
            $newTokens = $tokens->add($postWeight)
                                ->subtract($preWeight);
            $builder->assign($place, $newTokens);
        }
        return $builder->getMarking($this);
    }

    public function reachable(IMarking $marking): IMarkingMap {
        $map = new MarkingMap();
        foreach($this->enabledTransitions($marking) as $transition) {
            $newMarking = $this->fire($marking, $transition);
            $map->put($transition, $newMarking);
        }
        return $map;
    }

    public function enabledTransitions(IMarking $marking): TransitionContainer {
        $container = new TransitionContainer();
        foreach($this->transitions as $transition)
            if ($this->enabled($marking, $transition))
                $container->add($transition);
        return $container;
    }

    public function preset(IElement $e): PrePostSetMap {
        $flows = $this->flows;
        $map = new PrePostSetMap();
        foreach($flows as $flow => $weight)
            if ($flow->getTo() == $e)
                $map->put($flow->getFrom(), $weight);
        return $map;
    }

    public function postset(IElement $e): PrePostSetMap {
        $flows = $this->flows;
        $map = new PrePostSetMap();
        foreach($flows as $flow => $weight)
            if ($flow->getFrom() == $e)
                $map->put($flow->getTo(), $weight);
        return $map;
    }

    public function getPlaces(): IPlaces {
        return $this->places;
    }

    public function getTransitions(): ITransitions {
        return $this->transitions;
    }

    public function getFlows(): IFlows {
        return $this->flows;
    }

    public function jsonSerialize(): mixed {
        return [
            "places"      => $this->getPlaces(),
            "transitions" => $this->getTransitions(),
            "flows"       => $this->getFlows()
        ];
    }
}
