<?php

namespace Cora\Domain\Systems\Tokens;

class OmegaTokenCount extends AbstractTokenCount
{
    public function add($b){return $this;}
    public function subtract($b){return $this;}

    public function greater($b)
    {
        return true;
    }

    public function geq($b)
    {
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
?>
