<?php

namespace Cora\Domain\Graph;

use Cora\Domain\Graph\VertexMapInterface as IVertexMap;
use Cora\Domain\Graph\EdgeMapInterface as IEdgeMap;
use Cora\Domain\Graph\EdgeInterface as Edge;
use Cora\Domain\Graph\EdgeMap;

use Exception;

class Graph implements GraphInterface {
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

    public function getInitial(): ?int {
        return $this->initial;
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

    public function getVertexes(): IVertexMap {
        return $this->vertexes;
    }

    public function getEdges(): IEdgeMap {
        return $this->edges;
    }

    public function jsonSerialize(): mixed {
        return [
            "states" => $this->getVertexes(),
            "edges" => $this->getEdges(),
            "initial" => $this->getInitial()
        ];
    }
}
