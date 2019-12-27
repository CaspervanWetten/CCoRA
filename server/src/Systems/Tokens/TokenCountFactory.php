<?php

namespace Cora\Systems\Tokens;

class TokenCountFactory
{
   /**
    * Get a new TokenCount object depending on the qualifier that is given
    * If the qualifier is an integer, an IntegerTokenCount object is returned,
    * otherwise an OmegaTokenCount object is returned.
    * @param mixed The qualifier for the TokenCount object
    * @return \Cora\Systems\TokenCount The correct TokenCount object
    **/
    public static function getTokenCount($qualifier)
    {
        if(is_numeric($qualifier) || $qualifier instanceof IntegerTokenCount) {
            if(is_numeric($qualifier))
                return new IntegerTokenCount(intval($qualifier));
            else
                return $qualifier;
        }
        else {
            return new OmegaTokenCount();
        }
    }
}
