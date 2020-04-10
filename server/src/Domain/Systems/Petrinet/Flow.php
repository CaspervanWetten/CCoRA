<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;

class Flow implements FlowInterface {
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
}
