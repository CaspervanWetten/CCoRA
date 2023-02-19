<?php

namespace Cora\Domain\Graph;

class Edge implements EdgeInterface {
    protected $from;
    protected $to;
    protected $label;

    public function __construct(int $from, int $to, string $label) {
        $this->from = $from;
        $this->to = $to;
        $this->label = $label;
    }

    public function getFrom(): int {
        return $this->from;
    }

    public function getTo(): int {
        return $this->to;
    }

    public function getLabel(): string {
        return $this->label;
    }

    public function jsonSerialize(): mixed {
        return [
            "fromId" => $this->getFrom(),
            "toId"   => $this->getTo(),
            "label"  => $this->getLabel()
        ];
    }
}
