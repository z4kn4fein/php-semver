<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer\Traits;

use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

/**
 * This trait used to sort Version arrays.
 */
trait Sortable
{
    /**
     * Sorts an array of versions.
     *
     * @param Version[] $versions the versions to sort
     *
     * @return Version[] the sorted array of versions
     */
    public static function sort(array $versions): array
    {
        $sorted = $versions;
        usort($sorted, ['z4kn4fein\SemVer\Version', 'compare']);

        return $sorted;
    }

    /**
     * Sorts an array of versions in reverse order.
     *
     * @param Version[] $versions the versions to sort
     *
     * @return Version[] the sorted array of versions
     */
    public static function rsort(array $versions): array
    {
        $sorted = $versions;
        usort($sorted, function (Version $v1, Version $v2) {
            $result = self::compare($v1, $v2);

            switch ($result) {
                case -1:
                    return 1;

                case 1:
                    return -1;

                default:
                    return 0;
            }
        });

        return $sorted;
    }

    /**
     * Sorts an array of version strings.
     *
     * @param string[] $versions the version strings to sort
     *
     * @return string[] the sorted array of version strings
     *
     * @throws SemverException when the given array contains an invalid version string
     */
    public static function sortString(array $versions): array
    {
        $sorted = $versions;
        usort($sorted, function (string $v1, string $v2) {
            return self::compareString($v1, $v2);
        });

        return $sorted;
    }

    /**
     * Sorts an array of version strings in reverse order.
     *
     * @param string[] $versions the version strings to sort
     *
     * @return string[] the sorted array of version strings
     *
     * @throws SemverException when the given array contains an invalid version string
     */
    public static function rsortString(array $versions): array
    {
        $sorted = $versions;
        usort($sorted, function (string $v1, string $v2) {
            $result = self::compareString($v1, $v2);

            switch ($result) {
                case -1:
                    return 1;

                case 1:
                    return -1;

                default:
                    return 0;
            }
        });

        return $sorted;
    }
}
