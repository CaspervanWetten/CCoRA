<?php

namespace Cora\Domain\Systems\Graphs;

class Edge2 implements EdgeInterface {
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
}