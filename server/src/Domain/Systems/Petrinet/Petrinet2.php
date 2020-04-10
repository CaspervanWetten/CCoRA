<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;
use Cora\Domain\Systems\Petrinet\PetrinetElementType as ElementType;
use Cora\Domain\Systems\Petrinet\PetrinetElementContainerInterface as IContainer;
use Cora\Domain\Systems\Petrinet\PetrinetElementContainer as Container;
use Cora\Domain\Systems\Petrinet\FlowMapInterface as FlowMap;
use Cora\Domain\Systems\Petrinet\PrePostSetMapInterface as PrePostSetMap;
use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\MarkingMapInterface as IMarkingMap;
use Cora\Domain\Systems\MarkingMap;
use Cora\Domain\Systems\MarkingBuilder;
use Cora\Domain\Systems\Tokens\IntegerTokenCount;

use Exception;

class Petrinet2 implements PetrinetInterface {
    protected $places;
    protected $transitions;
    protected $flows;

    public function __construct (
        IContainer $p,
        IContainer $t,
        FlowMap $f)
    {
        $this->places = $p;
        $this->transitions = $t;
        $this->flows = $f;
    }

    public function enabled(Marking $marking, Element $e): bool {
        if ($e->getType() == ElementType::PLACE)
            throw new Exception("A place cannot be enabled or not");
        $preset = $this->preset($e);
        foreach($preset as $place => $weight) {
            $t = $marking->get($place);
            if ($t instanceof IntegerTokenCount && $t->value < $weight)
                return false;
        }
        return true;
    }

    public function fire(Marking $marking, Element $t): Marking {
        if ($t->getType() == ElementType::PLACE)
            throw new Exception("A place cannot be fired");
        $pre = $this->preset($t);
        $post = $this->postset($t);
        
        $builder = new MarkingBuilder();
        foreach($marking as $place => $tokens) {
            $newTokens = $tokens->add($post->get($place))
                                ->subtract($pre->get($place));
            $builder->assign($place, $newTokens);
        }
        return $builder->getMarking($this);
    }

    public function reachable(Marking $marking): IMarkingMap {
        $map = new MarkingMap();
        foreach($this->enabledTransitions($marking) as $transition) {
            $newMarking = $this->fire($marking, $transition);
            $map->put($transition, $newMarking);
        }
        return $map;
    }

    public function enabledTransitions(Marking $marking): IContainer {
        $container = new Container();
        foreach($this->transitions as $transition)
            if ($this->enabled($marking, $transition))
                $container->add($transition);
        return $container;
    }

    public function preset(Element $e): PrePostSetMap {
        $flows = $this->flows;
        $map = new PrePostSetMap();
        foreach($flows as $flow => $weight) 
            if ($flow->getTo() == $e) 
                $map->put($flow->getFrom(), $weight);
        return $map;
    }

    public function postset(Element $e): PrePostSetMap {
        $flows = $this->flows;
        $map = new PrePostSetMap();
        foreach($flows as $flow => $weight) 
            if ($flow->getFrom() == $e) 
                $map->put($flow->getTo(), $weight);
        return $map;
    }

    public function getPlaces(): IContainer {
        return $this->places;
    }

    public function getTransitions(): IContainer {
        return $this->transitions;
    }

    public function getFlows(): FlowMapInterface {
        return $this->flows;
    }
}
