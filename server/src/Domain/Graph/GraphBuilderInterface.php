<?php

namespace Cora\Domain\Graph;

use Cora\Domain\Graph\EdgeInterface as Edge;
use Cora\Domain\Graph\GraphInterface as Graph;

interface GraphBuilderInterface {
    public function addVertex(int $id, $vertex): void;
    public function addEdge(int $id, Edge $edge): void;
    public function setInitial(?int $id): void;
    public function getGraph(): Graph;
}
