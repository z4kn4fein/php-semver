<?php

namespace z4kn4fein\SemVer\Traits;

use z4kn4fein\SemVer\PreRelease;
use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

/**
 * This trait adds compare functions to Version.
 *
 * @package z4kn4fein\SemVer\Traits
 */
trait Comparable
{
    use PrimitiveComparable;

    /**
     * Compares the version with the given one, returns true when the current is less than the other.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance < $v, otherwise false.
     */
    public function isLessThan(Version $v): bool
    {
        return self::compare($this, $v) < 0;
    }

    /**
     * Compares the version with the given one, returns true when the current is less than the other or equal.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance <= $v, otherwise false.
     */
    public function isLessThanOrEqual(Version $v): bool
    {
        return self::compare($this, $v) <= 0;
    }

    /**
     * Compares the version with the given one, returns true when the current is greater than the other.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance > $v, otherwise false.
     */
    public function isGreaterThan(Version $v): bool
    {
        return self::compare($this, $v) > 0;
    }

    /**
     * Compares the version with the given one, returns true when the current is greater than the other or equal.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance >= $v, otherwise false.
     */
    public function isGreaterThanOrEqual(Version $v): bool
    {
        return self::compare($this, $v) >= 0;
    }

    /**
     * Compares the version with the given one, returns true when they are equal.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance == $v, otherwise false.
     */
    public function isEqual(Version $v): bool
    {
        return self::compare($this, $v) === 0;
    }

    /**
     * Compares the version with the given one, returns true when they are not equal.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance != $v, otherwise false.
     */
    public function isNotEqual(Version $v): bool
    {
        return self::compare($this, $v) !== 0;
    }

    /**
     * Compares two version strings and returns true when the first is less than the second.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 &lt; $v2, otherwise false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function lessThan(string $v1, string $v2): bool
    {
        $version1 = self::parse($v1);
        $version2 = self::parse($v2);

        return $version1->isLessThan($version2);
    }

    /**
     * Compares two version strings and returns true when the first is less than the second or equal.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 &lt;= $v2, otherwise false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function lessThanOrEqual(string $v1, string $v2): bool
    {
        $version1 = self::parse($v1);
        $version2 = self::parse($v2);

        return $version1->isLessThanOrEqual($version2);
    }

    /**
     * Compares two version strings and returns true when the first is greater than the second.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 &gt; $v2, otherwise false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function greaterThan(string $v1, string $v2): bool
    {
        $version1 = self::parse($v1);
        $version2 = self::parse($v2);

        return $version1->isGreaterThan($version2);
    }

    /**
     * Compares two version strings and returns true when the first is greater than the second or equal.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 &gt;= $v2, otherwise false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function greaterThanOrEqual(string $v1, string $v2): bool
    {
        $version1 = self::parse($v1);
        $version2 = self::parse($v2);

        return $version1->isGreaterThanOrEqual($version2);
    }

    /**
     * Compares two version strings and returns true when the first and second are equal.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 == $v2, otherwise false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function equal(string $v1, string $v2): bool
    {
        $version1 = self::parse($v1);
        $version2 = self::parse($v2);

        return $version1->isEqual($version2);
    }

    /**
     * Compares two version strings and returns true when the first and second are not equal.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 != $v2, otherwise false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function notEqual(string $v1, string $v2): bool
    {
        $version1 = self::parse($v1);
        $version2 = self::parse($v2);

        return $version1->isNotEqual($version2);
    }

    /**
     * Compares two version strings.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return int -1 when $v1 < $v2, 0 when $v1 == $v2, 1 when $v1 > $v2.
     * @throws SemverException When the version strings are invalid.
     */
    public static function compareString(string $v1, string $v2): int
    {
        $version1 = self::parse($v1);
        $version2 = self::parse($v2);

        return self::compare($version1, $version2);
    }

    /**
     * Compares two versions.
     *
     * @param Version $v1 The left side of the comparison.
     * @param Version $v2 The right side of the comparison.
     * @return int -1 when $v1 < $v2, 0 when $v1 == $v2, 1 when $v1 > $v2.
     */
    public static function compare(Version $v1, Version $v2): int
    {
        $major = self::comparePrimitive($v1->getMajor(), $v2->getMajor());
        if ($major != 0) {
            return $major;
        }

        $minor = self::comparePrimitive($v1->getMinor(), $v2->getMinor());
        if ($minor != 0) {
            return $minor;
        }

        $patch = self::comparePrimitive($v1->getPatch(), $v2->getPatch());
        if ($patch != 0) {
            return $patch;
        }

        return self::compareByPreRelease($v1, $v2);
    }

    /**
     * @param Version $v1 The left side of the comparison.
     * @param Version $v2 The right side of the comparison.
     * @return int -1 when $v1 < $v2, 0 when $v1 == $v2, 1 when $v1 > $v2.
     */
    private static function compareByPreRelease(Version $v1, Version $v2): int
    {
        if ($v1->isPreRelease() && !$v2->isPreRelease()) {
            return -1;
        }

        if (!$v1->isPreRelease() && $v2->isPreRelease()) {
            return 1;
        }

        if (!is_null($v1->preRelease) && !is_null($v2->preRelease)) {
            return PreRelease::compare($v1->preRelease, $v2->preRelease);
        }

        return 0;
    }
}
