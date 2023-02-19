<?php

namespace Cora\Domain\Petrinet;

use Cora\Domain\Petrinet\PetrinetElementInterface as Element;

use Ds\Map;
use Traversable;

class PrePostSetMap implements PrePostSetMapInterface {
    protected $map;

    public function __construct() {
        $this->map = new Map();
    }

    public function has(Element $e): bool {
        return $this->map->hasKey($e);
    }

    public function get(Element $e, int $default=0): int {
        return $this->map->get($e, $default);
    }

    public function put(Element $e, $w): void {
        $this->map->put($e, $w);
    }

    public function getIterator(): Traversable {
        return $this->map->getIterator();
    }
}
