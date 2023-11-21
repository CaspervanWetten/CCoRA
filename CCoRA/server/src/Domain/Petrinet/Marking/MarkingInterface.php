<?php

namespace Cora\Domain\Petrinet\Marking;

use Cora\Domain\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Petrinet\Place\Place;
use Cora\Domain\Petrinet\Place\PlaceContainerInterface as Places;
use Cora\Domain\Petrinet\Marking\Tokens\TokenCountInterface as Tokens;

use Ds\Hashable;
use IteratorAggregate;
use JsonSerializable;

interface MarkingInterface extends IteratorAggregate, JsonSerializable, Hashable {
    public function get(Place $place): Tokens;
    public function places(): Places;
    public function unbounded(): Places;
    public function covers(MarkingInterface $other, Petrinet $net): bool;
    public function covered(MarkingInterface $other, Petrinet $net): Places;
    public function withUnbounded(Petrinet $net, Places $unbounded): MarkingInterface;
}
