<?php

namespace Cora\Domain\Systems;

use Cora\Domain\Systems\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;
use Cora\Domain\Systems\Petrinet\PetrinetElementContainerInterface as Container;
use Cora\Domain\Systems\Tokens\TokenCountInterface as Tokens;

use Ds\Set;
use IteratorAggregate;

interface MarkingInterface extends IteratorAggregate {
    public function get(Element $place): Tokens;
    public function unbounded(): Set;
    public function covers(Marking $other, Petrinet $net): bool;
    public function covered(Marking $other, Petrinet $net): Set;
    public function withUnbounded(Petrinet $net, Container $unbounded): MarkingInterface;
}
