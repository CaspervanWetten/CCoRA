<?php

namespace Cora\Domain\Graph;

use Ds\Map;
use Ds\Set;

use Traversable;

class VertexMap implements VertexMapInterface {
    protected $map;

    public function __construct() {
        $this->map = new Map();
    }

    public function hasVertex(int $id): bool {
        return $this->map->hasKey($id);
    }

    public function getVertex(int $id) {
        return $this->map->get($id);
    }

    public function addVertex(int $id, $vertex): void {
        $this->map->put($id, $vertex);
    }

    public function getIds(): Set {
        return $this->map->keys();
    }

    public function jsonSerialize(): mixed {
        return $this->map;
    }

    public function getIterator(): Traversable {
        return $this->map->getIterator();
    }
}
