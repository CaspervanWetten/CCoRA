<?php

namespace Cora\Domain\Systems\Petrinet\Marking\Tokens;

class OmegaTokenCount extends AbstractTokenCount {
    public function add($b): TokenCountInterface {return $this;}
    public function subtract($b): TokenCountInterface {return $this;}

    public function greater($b): bool {
        return true;
    }

    public function geq($b): bool {
        return true;
    }

    public function hash() {
        return strval($this);
    }

    public function equals($other): bool {
        return true;
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }

    public function __toString() {
        return sprintf("%s", "OMEGA");
    }
}
