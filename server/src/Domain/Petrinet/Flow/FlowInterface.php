<?php

namespace Cora\Domain\Petrinet\Flow;

use Cora\Domain\Petrinet\PetrinetElementInterface;

use JsonSerializable;

interface FlowInterface extends JsonSerializable {
    public function getFrom(): PetrinetElementInterface;
    public function getTo(): PetrinetElementInterface;
}
