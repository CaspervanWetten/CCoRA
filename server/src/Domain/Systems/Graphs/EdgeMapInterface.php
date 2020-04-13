<?php

namespace Cora\Domain\Systems\Graphs;

use Cora\Domain\Systems\Graphs\EdgeInterface as Edge;

use Ds\Set;
use IteratorAggregate;

interface EdgeMapInterface extends IteratorAggregate {
    public function addEdge(int $id, Edge $edge): void;
    public function getEdge(int $id): Edge;
    public function hasEdge(int $id): bool;
    public function getIds(): Set;
    public function isEmpty(): bool;
}
