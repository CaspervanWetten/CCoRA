<?php

namespace Cora\Domain\Systems\Graphs;

use Cora\Domain\Systems\Graphs\EdgeInterface as Edge;
use Cora\Domain\Systems\Graphs\EdgeMapInterface as EdgeMap;
use Cora\Domain\Systems\Graphs\VertexMapInterface as VertexMap;

use JsonSerializable;

interface GraphInterface extends JsonSerializable {
    public function preset(int $id): EdgeMap;
    public function postset(int $id): EdgeMap;
    public function getVertex(int $id);
    public function getVertexes(): VertexMap;
    public function getEdge(int $id): Edge;
    public function hasVertex(int $id): bool;
    public function hasEdge(int $id): bool;
    public function getInitial(): ?int;
}
