<?php

namespace Cora\Domain\Graphs;

use Cora\Domain\Graphs\EdgeInterface as Edge;

use Ds\Map;
use Ds\Set;

class EdgeMap implements EdgeMapInterface {
    protected $map;

    public function __construct() {
        $this->map = new Map();
    }

    public function addEdge(int $id, Edge $edge): void {
        $this->map->put($id, $edge);
    }

    public function hasEdge(int $id): bool {
        return $this->map->hasKey($id);
    }

    public function getEdge(int $id): Edge {
        return $this->map->get($id);
    }

    public function getIds(): Set {
        return $this->map->keys();
    }

    public function isEmpty(): bool {
        return $this->map->isEmpty();
    }

    public function jsonSerialize() {
        return $this->map;
    }

    public function getIterator() {
        return $this->map->getIterator();
    }
}
