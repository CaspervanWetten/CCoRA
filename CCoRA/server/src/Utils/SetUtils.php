<?php

namespace Cora\Utils;

use Ds\Set;

class SetUtils {
   /**
    * Determine whether the lhs is a subset of the rhs
    * @param Set lhs The left hand side of the equation
    * @param Set rhs the right hand side of the equation
    * @return bool Whether lhs is subset of rhs
    **/
    public static function isSubset(Set $lhs, Set $rhs) {
        foreach($lhs as $element)
            if (!$rhs->contains($element))
                return FALSE;
        return TRUE;
    }

    public static function isStrictSubset(Set $lhs, Set $rhs) {
        return self::isSubset($lhs, $rhs) && $lhs->count() < $rhs->count();
    }

    public static function isSuperSet(Set $lhs, Set $rhs) {
        return self::isSubset($rhs, $lhs);
    }

    public static function isStrictSuperSet(Set $lhs, Set $rhs) {
        return self::isStrictSubSet($rhs, $lhs);
    }

    public static function areDisjoint(Set $lhs, Set $rhs): bool {
        $intersection = $lhs->intersect($rhs);
        return $intersection->isEmpty();
    }
}
