<?php

namespace Cora\Domain\Systems;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Petrinet\Place;
use Cora\Domain\Systems\Petrinet\PlaceContainerInterface as Places;
use Cora\Domain\Systems\MarkingInterface as Marking;
use Cora\Domain\Systems\Tokens\TokenCountInterface as Tokens;

use IteratorAggregate;
use JsonSerializable;

interface MarkingInterface extends IteratorAggregate, JsonSerializable {
    public function get(Place $place): Tokens;
    public function unbounded(): Places;
    public function covers(Marking $other, Petrinet $net): bool;
    public function covered(Marking $other, Petrinet $net): Places;
    public function withUnbounded(Petrinet $net, Places $unbounded): MarkingInterface;
}
