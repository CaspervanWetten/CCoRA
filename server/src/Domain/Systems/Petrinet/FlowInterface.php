<?php

namespace Cora\Domain\Systems\Petrinet;

use JsonSerializable;

interface FlowInterface extends JsonSerializable {
    public function getFrom(): PetrinetElementInterface;
    public function getTo(): PetrinetElementInterface;
}
