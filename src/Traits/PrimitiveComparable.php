<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer\Traits;

/**
 * @internal
 */
trait PrimitiveComparable
{
    /**
     * @param int|string $a the left side of the comparison
     * @param int|string $b the right side of the comparison
     *
     * @return int -1 when $a < $b, 0 when $a == $b, 1 when $a > $b
     */
    private static function comparePrimitive(int|string $a, int|string $b): int
    {
        if ($a != $b) {
            return $a < $b ? -1 : 1;
        }

        return 0;
    }
}
