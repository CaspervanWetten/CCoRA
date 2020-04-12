<?php

namespace Cora\Domain\Systems\Graphs;

use Cora\Domain\Systems\Graphs\VertexMapInterface as IVertexMap;
use Cora\Domain\Systems\Graphs\EdgeMapInterface as IEdgeMap;
use Cora\Domain\Systems\Graphs\EdgeInterface as Edge;
use Cora\Domain\SYstems\Graphs\EdgeMap;

use Exception;

class Graph2 implements GraphInterface {
    protected $vertexes;
    protected $edges;
    protected $initial;

    public function __construct(
        IVertexMap $vertexes,
        IEdgeMap $edges,
        ?int $initial)
    {
        $this->vertexes = $vertexes;
        $this->edges = $edges;
        $this->initial = $initial;
    }

    public function getVertex(int $id) {
        return $this->vertexes->getVertex($id);
    }

    public function getEdge(int $id): Edge {
        return $this->edges->getEdge($id);
    }

    public function hasVertex(int $id): bool {
        return $this->vertexes->hasVertex($id);
    }

    public function hasEdge(int $id): bool {
        return $this->edges->hasEdge($id);
    }

    public function preset(int $id): IEdgeMap {
        if (!$this->hasVertex($id))
            throw new Exception("Could not retrieve preset: " .
                                "invalid id: $id");
        $res = new EdgeMap();
        foreach($this->edges as $edgeId => $edge)
            if ($edge->getTo() === $id)
                $res->addEdge($edgeId, $edge);
        return $res;
    }

    public function postset(int $id): IEdgeMap {
        if (!$this->hasVertex($id))
            throw new Exception("Could not retrieve postset: " .
                                "invalid id: $id");
        $res = new EdgeMap();
        foreach($this->edges as $edgeId => $edge)
            if ($edge->getFrom() === $id)
                $res->addEdge($edgeId, $edge);
        return $res;
    }
}
