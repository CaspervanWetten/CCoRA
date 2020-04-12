<?php

namespace Cora\Domain\Systems;

use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\Petrinet\Place;
use Cora\Domain\Systems\Petrinet\PlaceContainer;
use Cora\Domain\Systems\Petrinet\PlaceContainerInterface as Places;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Tokens\IntegerTokenCount;
use Cora\Domain\Systems\Tokens\OmegaTokenCount;
use Cora\Domain\Systems\Tokens\TokenCountInterface as Tokens;

use Ds\Map;
use Exception;

class Marking2 implements MarkingInterface {
    protected $map;

    public function __construct(Map $map) {
        $this->map = $map;
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

    public function covers(Marking $other, Petrinet $net): bool {
        foreach($net->getPlaces() as $place) {
            $tokens = $this->get($place);
            if (!$tokens->geq($other->get($place)))
                return false;
        }
        return true;
    }

    public function covered(Marking $other, Petrinet $net): Places {
        $res = new PlaceContainer();
        if ($this->covers($other, $net)) 
            foreach($net->getPlaces() as $place)
                if($this->get($place)->greater($other->get($place)))
                    $res->add($place);
        return $res;
    }

    public function withUnbounded(Petrinet $net, Places $unbounded): Marking {
        $places = $net->getPlaces();
        $newMap = clone $this->map;
        foreach($unbounded as $place) {
            if (!$places->contains($place))
                throw new Exception("Can not mark place as unbounded: not " .
                                    "part of the Petri net");
            $newMap->put($place, new OmegaTokenCount());
        }
        return new Marking2($newMap);
    }

    public function hash() {
        $a = $this->map->toArray();
        $result = [];
        foreach($a as $place => $tokens)
            if (!($tokens instanceof IntegerTokenCount &&
                  $tokens->value == 0))
                $result[$place] = $tokens;
        return $result;
    }

    public function equals($other): bool {
        return $this->hash() === $other->hash();
    }

    public function getIterator() {
        return $this->map->getIterator();
    }

    public function jsonSerialize() {
        $result = [];
        foreach($this->map as $place => $tokens) 
            $result[$place->getName()] = $tokens;
        return $result;
    }
}
