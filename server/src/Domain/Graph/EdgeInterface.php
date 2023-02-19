<?php

namespace Cora\Domain\Graph;

use JsonSerializable;

interface EdgeInterface extends JsonSerializable {
    public function getFrom(): int;
    public function getTo(): int;
    public function getLabel(): string;
}
