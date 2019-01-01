<?php

namespace Cora\Systems;

abstract class TokenCount implements \JsonSerializable
{
    public abstract function add($b);
    public abstract function subtract($b);
    public abstract function greater($b);   // greater than
    public abstract function geq($b);       // greather or equal to
}

?>
