<?php

namespace Cora\Domain\Graphs;

use Cora\Domain\Graphs\EdgeInterface as Edge;
use Cora\Domain\Graphs\GraphInterface as Graph;

interface GraphBuilderInterface {
    public function addVertex(int $id, $vertex): void;
    public function addEdge(int $id, Edge $edge): void;
    public function setInitial(int $id): void;
    public function getGraph(): Graph;
}
