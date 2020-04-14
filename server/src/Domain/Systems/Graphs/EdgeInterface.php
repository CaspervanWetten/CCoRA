<?php

namespace Cora\Domain\Systems\Graphs;

use JsonSerializable;

interface EdgeInterface extends JsonSerializable {
    public function getFrom(): int;
    public function getTo(): int;
    public function getLabel(): string;
}
