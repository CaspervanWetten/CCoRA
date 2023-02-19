<?php

namespace Cora\Domain\Petrinet\Marking;

use Cora\Domain\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Domain\Petrinet\Place\Place;
use Cora\Domain\Petrinet\Place\PlaceContainer;
use Cora\Domain\Petrinet\Place\PlaceContainerInterface as Places;
use Cora\Domain\Petrinet\Marking\MarkingInterface as IMarking;

use Cora\Domain\Petrinet\Marking\Tokens\IntegerTokenCount;
use Cora\Domain\Petrinet\Marking\Tokens\OmegaTokenCount;
use Cora\Domain\Petrinet\Marking\Tokens\TokenCountInterface as Tokens;

use Ds\Map;
use Traversable;

use Exception;

class Marking implements IMarking {
    protected $map;

    public function __construct(Map $map) {
        $filtered = new Map();
        foreach($map as $place => $tokens)
            if (!($tokens instanceof IntegerTokenCount &&
                  $tokens->getValue() === 0))
                $filtered->put($place, $tokens);
        $this->map = $filtered;
    }

    public function get(Place $place): Tokens {
        if ($this->map->hasKey($place))
            return $this->map->get($place);
        return new IntegerTokenCount(0);
    }

    public function places(): Places {
        $container = new PlaceContainer();
        $p = $this->map->keys();
        foreach($p as $place)
            $container->add($place);
        return $container;
    }

    public function unbounded(): Places {
        $result = new PlaceContainer();
        foreach($this->map as $place => $tokens)
            if ($tokens instanceof OmegaTokenCount)
                $result->add($place);
        return $result;
    }

    public function covers(IMarking $other, IPetrinet $net): bool {
        foreach($net->getPlaces() as $place) {
            $tokens = $this->get($place);
            if (!$tokens->geq($other->get($place)))
                return false;
        }
        return true;
    }

    public function covered(IMarking $other, IPetrinet $net): Places {
        $res = new PlaceContainer();
        if ($this->covers($other, $net))
            foreach($net->getPlaces() as $place)
                if($this->get($place)->greater($other->get($place)))
                    $res->add($place);
        return $res;
    }

    public function withUnbounded(IPetrinet $net, Places $unbounded): IMarking {
        $places = $net->getPlaces();
        $newMap = clone $this->map;
        foreach($unbounded as $place) {
            if (!$places->contains($place))
                throw new Exception("Can not mark place as unbounded: not " .
                                    "part of the Petri net");
            $newMap->put($place, new OmegaTokenCount());
        }
        return new Marking($newMap);
    }

    public function hash() {
        $result = [];
        foreach($this->map as $place => $tokens) {
            if (!($tokens instanceof IntegerTokenCount &&
                  $tokens->getValue() == 0))
                $result[$place->hash()] = $tokens->hash();
        }
        return $result;
    }

    public function equals($other): bool {
        return $this->hash() === $other->hash();
    }

    public function getIterator(): Traversable {
        return $this->map->getIterator();
    }

    public function jsonSerialize(): mixed {
        $result = [];
        foreach($this->map as $place => $tokens)
            $result[$place->getName()] = $tokens;
        return $result;
    }
}
