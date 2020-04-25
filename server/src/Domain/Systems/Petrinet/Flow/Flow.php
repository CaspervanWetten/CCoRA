<?php

namespace Cora\Domain\Systems\Petrinet\Flow;

use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;

use Ds\Hashable;

class Flow implements FlowInterface, Hashable {
    protected $from;
    protected $to;

    public function __construct(Element $from, Element $to) {
        $this->from = $from;
        $this->to   = $to;
    }

    public function getFrom(): Element {
        return $this->from;
    }

    public function getTo(): Element {
        return $this->to;
    }

    public function hash() {
        return $this->from->hash() . $this->to->hash();
    }

    public function equals($other): bool {
        return $this->getFrom() === $other->getFrom() &&
               $this->getTo() === $other->getTo();
    }

    public function jsonSerialize() {
        return [
            "from" => $this->getFrom(),
            "to" => $this->getTo()
        ];
    }
}
