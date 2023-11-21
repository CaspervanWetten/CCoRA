<?php

namespace Cora\Domain\Petrinet\Marking\Tokens;

use JsonSerializable;

abstract class AbstractTokenCount implements TokenCountInterface, JsonSerializable {
   /**
    * Add a TokenCount object to this one
    * @param TokenCount $b The TokenCount to add
    * @return TokenCount The resulting TokenCount object
    **/
    public abstract function add($b): TokenCountInterface;

   /**
    * Subtract a TokenCount object from this one
    * @param TokenCount $b The TokenCount to subtract
    * @return TokenCount The resulting TokenCount object
    **/
    public abstract function subtract($b): TokenCountInterface;

   /**
    * Determine whether this TokenCount object is greater than
    * ( > ) the provided TokenCount object.
    * @param TokenCount $b The TokenCount to compare to
    * @return bool True if greater, false otherwise
    **/
    public abstract function greater($b): bool;

   /**
    * Determine whether this TokenCount object is greater or equal
    * to the provided TokenCount object.
    * @param TokenCount $b The TokenCount to compare to
    * @return bool True if greater or equal, false otherwis.
    **/
    public abstract function geq($b): bool;
}
