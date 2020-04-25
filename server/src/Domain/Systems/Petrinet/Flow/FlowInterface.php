<?php

namespace Cora\Domain\Systems\Petrinet\Flow;

use Cora\Domain\Systems\Petrinet\PetrinetElementInterface;

use JsonSerializable;

interface FlowInterface extends JsonSerializable {
    public function getFrom(): PetrinetElementInterface;
    public function getTo(): PetrinetElementInterface;
}
