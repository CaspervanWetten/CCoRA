<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;

use Ds\Map;

class PrePostSetMap implements PrePostSetMapInterface {
    protected $map;

    public function __construct() {
        $this->map = new Map();
    }

    public function has(Element $e): bool {
        return $this->map->hasKey($e);
    }

    public function get(Element $e): int {
        return $this->map->get($e);
    }

    public function put(Element $e, $w): void {
        $this->map->put($e, $w);
    }

    public function getIterator() {
        return $this->map->getIterator();
    }
}
