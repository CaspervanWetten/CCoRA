<?php

namespace Cora\Domain\Systems\Graphs;

interface EdgeInterface {
    public function getFrom(): int;
    public function getTo(): int;
    public function getLabel(): string;
}
