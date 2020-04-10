<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\FlowInterface as Flow;
use Ds\Map;

class FlowMap implements FlowMapInterface {
    protected $map;

    public function __construct() {
        $this->map = new Map();
    }

    public function add(Flow $flow, int $weight=1): void {
        $this->map->put($flow, $weight);
    }

    public function has(Flow $flow): bool {
        return $this->map->hasKey($flow);
    }

    public function get(Flow $flow): int {
        if ($this->has($flow))
            return $this->map->get($flow);
        return 0;
    }

    public function getIterator() {
        return $this->map->getIterator();
    }
}
