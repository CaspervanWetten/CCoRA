<?php

namespace Cozp\Systems;

class TokenCountFactory
{
    public static function getTokenCount($qualifier)
    {
        if(is_numeric($qualifier) || $qualifier instanceof IntegerTokenCount) {
            if(is_numeric($qualifier))
                return new IntegerTokenCount($qualifier);
            else
                return $qualifier;
        }
        else {
            return new OmegaTokenCount();
        }
    }
}