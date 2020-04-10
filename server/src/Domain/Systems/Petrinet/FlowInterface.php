<?php

namespace Cora\Domain\Systems\Petrinet;

interface FlowInterface {
    public function getFrom(): PetrinetElementInterface;
    public function getTo(): PetrinetElementInterface;
}
