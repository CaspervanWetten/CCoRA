<?php

namespace Cora\Domain\Petrinet\Flow;

use Cora\Domain\Petrinet\Flow\FlowInterface as Flow;
use Cora\Domain\Petrinet\Flow\FlowContainerInterface as Flows;

use Ds\Map;
use Traversable;

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

    public function flows(): Flows {
        return new FlowContainer($this->map->keys());
    }

    public function getIterator(): Traversable {
        return $this->map->getIterator();
    }

    public function jsonSerialize(): mixed {
        $res = [];
        foreach($this->map as $flow => $weight)
            array_push($res, [
                "from"   => $flow->getFrom(),
                "to"     => $flow->getTo(),
                "weight" => $weight
            ]);
        return $res;
    }
}
