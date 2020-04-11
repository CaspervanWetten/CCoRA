<?php

namespace Cora\Domain\Systems\Petrinet;

use JsonSerializable;

interface PlaceContainerInterface extends JsonSerializable {
    public function contains(Place $place): bool;
    public function add(Place $place): void;
}
