<?php

namespace Cora\Domain\Systems\Graphs;

use Ds\Set;
use IteratorAggregate;

interface VertexMapInterface extends IteratorAggregate {
    public function hasVertex(int $id): bool;
    public function getVertex(int $id);
    public function addVertex(int $id, $vertex): void;
    public function getIds(): Set;
}
