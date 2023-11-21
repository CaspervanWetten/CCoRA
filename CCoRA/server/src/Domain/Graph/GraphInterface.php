<?php

namespace Cora\Domain\Graph;

use Cora\Domain\Graph\EdgeInterface as Edge;
use Cora\Domain\Graph\EdgeMapInterface as EdgeMap;
use Cora\Domain\Graph\VertexMapInterface as VertexMap;

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
