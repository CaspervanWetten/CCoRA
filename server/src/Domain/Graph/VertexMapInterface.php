<?php

namespace Cora\Domain\Graph;

use Ds\Set;
use IteratorAggregate;
use JsonSerializable;

interface VertexMapInterface extends IteratorAggregate, JsonSerializable {
    public function hasVertex(int $id): bool;
    public function getVertex(int $id);
    public function addVertex(int $id, $vertex): void;
    public function getIds(): Set;
}

