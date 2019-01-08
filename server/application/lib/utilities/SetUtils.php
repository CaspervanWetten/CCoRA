<?php

namespace Cora\Utils;

class SetUtils {
   /**
    * Determine whether the lhs is a subset of the rhs
    * @param Set lhs The left hand side of the equation
    * @param Set rhs the right hand side of the equation
    * @return bool Whether lhs is subset of rhs
    **/
    public static function isSubset($lhs, $rhs) {
        $intersect = $lhs->intersect($rhs);
        foreach($lhs as $element) {
            if(!$intersect->contains($element)) {
                return FALSE;
            }
        }
        return TRUE;
    }
}

?>
