<?php

namespace Cora\Domain\Petrinet\Place;

use Cora\Domain\Petrinet\PetrinetElementContainerInterface;

use IteratorAggregate;
use JsonSerializable;

interface PlaceContainerInterface extends
    PetrinetElementContainerInterface,
    JsonSerializable,
    IteratorAggregate
{
    public function contains(Place $place): bool;
    public function add(Place $place): void;
}
