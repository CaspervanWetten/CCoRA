<?php

namespace Cora\Domain\Systems\Petrinet;

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