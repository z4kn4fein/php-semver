<?php

namespace z4kn4fein\SemVer;

class Utils
{
    /**
     * @param $a int|string The left side of the comparison.
     * @param $b int|string The right side of the comparison.
     * @return int -1 when $a < $b, 0 when $a == $b, 1 when $a > $b.
     */
    public static function comparePrimitive($a, $b)
    {
        if ($a != $b) {
            return $a < $b ? -1 : 1;
        }

        return 0;
    }
}