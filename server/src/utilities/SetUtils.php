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
        foreach($lhs as $element) {
            if (!$rhs->contains($element)) {
                return FALSE;
            }
        }
        return TRUE;
    }

    public static function isStrictSubset($lhs, $rhs) {
        return self::isSubset($lhs, $rhs) && $lhs->count() < $rhs->count();
    }

    public static function isSuperSet($lhs, $rhs) {
        return self::isSubset($rhs, $lhs);
    }

    public static function isStrictSuperSet($lhs, $rhs) {
        return self::isStrictSubSet($rhs, $lhs);
    }
}

?>
