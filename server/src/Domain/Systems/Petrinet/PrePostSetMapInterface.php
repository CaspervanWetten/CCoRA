<?php

namespace Cora\Domain\Systems\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetElementInterface as Element;

use IteratorAggregate;

interface PrePostSetMapInterface extends IteratorAggregate {
    public function has(Element $e): bool;
    public function get(Element $e): int;
    public function put(Element $e, int $weight): void;
}
