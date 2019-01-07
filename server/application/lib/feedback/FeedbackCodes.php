<?php

namespace Cora\Feedback;

use \Cora\Enumerators as Enumerators;

class FeedbackCode extends Enumerators\Enumerator
{
    // initial state
    const CORRECT_INITIAL_STATE            = 200;
    const NO_INITIAL_STATE                 = 400;
    const INCORRECT_INITIAL_STATE          = 401;

    // states
    const REACHABLE_FROM_PRESET            = 220;
    const DUPLICATE_STATE                  = 320;    
    const NOT_REACHABLE_FROM_PRESET        = 420;
    const EDGE_MISSING                     = 421;
    const NOT_REACHABLE_FROM_INITIAL       = 422;

    // edges
    const ENABLED_CORRECT_POST             = 240;
    const DUPLICATE_EDGE                   = 340;   
    const ENABLED_CORRECT_POST_WRONG_LABEL = 440;
    const ENABLED_INCORRECT_POST           = 441;
    const DISABLED                         = 442;
    const DISABLED_CORRECT_POST            = 443;
    const MISSED_SELF_LOOP                 = 444;
}

?>
