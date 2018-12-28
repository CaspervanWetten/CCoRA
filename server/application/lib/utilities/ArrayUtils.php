<?php

namespace Cozp\Utils;

class ArrayUtils
{
    /**
     * Get the amount of dimensions an array contains
     * @param  array  $array    The array of which the amount of dimensions needs to
     * be calculated.
     * @param  integer $count   The current amount of found dimensions
     * @return integer          The found amount of dimensions
     */
    public static function getNumDimensions($array, int $count = 0)
    {
        if (is_array($array)) {
            return ArrayUtils::getNumDimensions(current($array), ++$count);
        }
        return $count;
    }
}
