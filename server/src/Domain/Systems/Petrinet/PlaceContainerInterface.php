<?php

namespace Cora\Domain\Systems\Petrinet;

interface PlaceContainerInterface {
    public function contains(Place $place): bool;
    public function add(Place $place): void;
}
