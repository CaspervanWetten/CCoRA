<?php

namespace Cora\Domain\Petrinet;

use Cora\Domain\Petrinet\PetrinetElementInterface as Element;

use IteratorAggregate;

interface PrePostSetMapInterface extends IteratorAggregate {
    public function has(Element $e): bool;
    public function get(Element $e, int $default): int;
    public function put(Element $e, int $weight): void;
}
