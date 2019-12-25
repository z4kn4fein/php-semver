<?php

namespace z4kn4fein\SemVer;

/**
 * Class Version This class describes a semantic version and related operations.
 * @package z4kn4fein\SemVer
 */
class Version
{
    const VERSION_REGEX = "/^(?P<major>0|[1-9]\d*)\.(?P<minor>0|[1-9]\d*)\.(?P<patch>0|[1-9]\d*)(?:-(?P<prerelease>(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+(?P<buildmetadata>[0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/";

    /** @var int */
    private $major;
    /** @var int */
    private $minor;
    /** @var int */
    private $patch;
    /** @var null|string */
    private $preRelease;
    /** @var null|string */
    private $buildMeta;
    /** @var array */
    private $preReleaseParts;
    /** @var string */
    private $versionString;

    /**
     * Version constructor.
     * @param $versionString string The version string.
     * @throws VersionFormatException When the $versionString is invalid.
     */
    public function __construct($versionString)
    {
        $versionString = trim($versionString);
        if (empty($versionString)) {
            throw new VersionFormatException("versionString cannot be empty.");
        }

        if (!preg_match(self::VERSION_REGEX, $versionString, $matches)) {
            throw new VersionFormatException(sprintf("Invalid version: %s.", $versionString));
        }

        $this->versionString = $versionString;
        $this->major = intval($matches['major']);
        $this->minor = intval($matches['minor']);
        $this->patch = intval($matches['patch']);
        $this->preRelease = isset($matches['prerelease']) ? $matches['prerelease'] : null;
        $this->buildMeta = isset($matches['buildmetadata']) ? $matches['buildmetadata'] : null;

        if (!empty($this->preRelease)) {
            $this->preReleaseParts = explode('.', $this->preRelease);
        }
    }

    /**
     * @return int
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @return null|string
     */
    public function getPreRelease()
    {
        return $this->preRelease;
    }

    /**
     * @return null|string
     */
    public function getBuildMeta()
    {
        return $this->buildMeta;
    }

    /**
     * @return array
     */
    public function getPreReleaseParts()
    {
        return $this->preReleaseParts;
    }

    /**
     * @return string
     */
    public function getVersionString()
    {
        return $this->versionString;
    }

    public function hasPreRelease()
    {
        return !empty($this->preReleaseParts);
    }

    /**
     * @param $v string|Version The the version to compare.
     * @return bool True when instance < $v. Otherwise false.
     * @throws VersionFormatException When the given version is invalid.
     */
    public function isLessThan($v)
    {
        return self::lessThan($this, $v);
    }

    /**
     * @param $v string|Version The the version to compare.
     * @return bool True when instance <= $v. Otherwise false.
     * @throws VersionFormatException When the given version is invalid.
     */
    public function isLessThanOrEqual($v)
    {
        return self::lessThanOrEqual($this, $v);
    }

    /**
     * @param $v string|Version The the version to compare.
     * @return bool True when instance > $v. Otherwise false.
     * @throws VersionFormatException When the given version is invalid.
     */
    public function isGreaterThan($v)
    {
        return self::greaterThan($this, $v);
    }

    /**
     * @param $v string|Version The the version to compare.
     * @return bool True when instance >= $v. Otherwise false.
     * @throws VersionFormatException When the given version is invalid.
     */
    public function isGreaterThanOrEqual($v)
    {
        return self::greaterThanOrEqual($this, $v);
    }

    /**
     * @param $v string|Version The the version to compare.
     * @return bool True when instance == $v. Otherwise false.
     * @throws VersionFormatException When the given version is invalid.
     */
    public function isEqual($v)
    {
        return self::equal($this, $v);
    }

    /**
     * @param $v string The version string.
     * @return Version The parsed version.
     * @throws VersionFormatException When the given version string is invalid.
     */
    public static function parse($v)
    {
        return new Version($v);
    }

    /**
     * @param $v1 string|Version The left side of the comparison.
     * @param $v2 string|Version The right side of the comparison.
     * @return bool True when $v1 < $v2. Otherwise false.
     * @throws VersionFormatException When the given versions are invalid.
     */
    public static function lessThan($v1, $v2)
    {
        return self::compare($v1, $v2) < 0;
    }

    /**
     * @param $v1 string|Version The left side of the comparison.
     * @param $v2 string|Version The right side of the comparison.
     * @return bool True when $v1 <= $v2. Otherwise false.
     * @throws VersionFormatException When the given versions are invalid.
     */
    public static function lessThanOrEqual($v1, $v2)
    {
        return self::compare($v1, $v2) <= 0;
    }

    /**
     * @param $v1 string|Version The left side of the comparison.
     * @param $v2 string|Version The right side of the comparison.
     * @return bool True when $v1 > $v2. Otherwise false.
     * @throws VersionFormatException When the given versions are invalid.
     */
    public static function greaterThan($v1, $v2)
    {
        return self::compare($v1, $v2) > 0;
    }

    /**
     * @param $v1 string|Version The left side of the comparison.
     * @param $v2 string|Version The right side of the comparison.
     * @return bool True when $v1 >= $v2. Otherwise false.
     * @throws VersionFormatException When the given versions are invalid.
     */
    public static function greaterThanOrEqual($v1, $v2)
    {
        return self::compare($v1, $v2) >= 0;
    }

    /**
     * @param $v1 string|Version The left side of the comparison.
     * @param $v2 string|Version The right side of the comparison.
     * @return bool True when $v1 == $v2. Otherwise false.
     * @throws VersionFormatException When the given versions are invalid.
     */
    public static function equal($v1, $v2)
    {
        return self::compare($v1, $v2) == 0;
    }

    /**
     * @param $v1 string|Version The left side of the comparison.
     * @param $v2 string|Version The right side of the comparison.
     * @return int -1 when $v1 < $v2, 0 when $v1 == $v2, 1 when $v1 > $v2.
     * @throws VersionFormatException When the given versions are invalid.
     */
    private static function compare($v1, $v2)
    {
        if (!$v1 instanceof Version) {
            $v1 = self::parse($v1);
        }

        if (!$v2 instanceof Version) {
            $v2 = self::parse($v2);
        }

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
     * @param $v1 Version The left side of the comparison.
     * @param $v2 Version The right side of the comparison.
     * @return int -1 when $v1 < $v2, 0 when $v1 == $v2, 1 when $v1 > $v2.
     */
    private static function compareByPreRelease($v1, $v2)
    {
        if ($v1->hasPreRelease() && !$v2->hasPreRelease()) {
            return -1;
        }

        if (!$v1->hasPreRelease() && $v2->hasPreRelease()) {
            return 1;
        }

        if ($v1->hasPreRelease() && $v2->hasPreRelease()) {
            return self::compareByPreReleaseParts($v1->getPreReleaseParts(), $v2->getPreReleaseParts());
        }

        return 0;
    }

    /**
     * @param $v1 array The left side of the comparison.
     * @param $v2 array The right side of the comparison.
     * @return int -1 when $v1 < $v2, 0 when $v1 == $v2, 1 when $v1 > $v2.
     */
    private static function compareByPreReleaseParts(array $v1, array $v2)
    {
        $v1Size = sizeof($v1);
        $v2Size = sizeof($v2);

        $count = $v1Size > $v2Size ? $v2Size : $v1Size;

        for ($i = 0; $i < $count; $i++) {
            $part = self::comparePart($v1[$i], $v2[$i]);
            if ($part != 0) {
                return $part;
            }
        }

        return self::comparePrimitive($v1Size, $v2Size);
    }

    /**
     * @param $a mixed The left side of the comparison.
     * @param $b mixed The right side of the comparison.
     * @return int -1 when $a < $b, 0 when $a == $b, 1 when $v1 > $b.
     */
    private static function comparePart($a, $b)
    {
        if (is_numeric($a) && !is_numeric($b)) {
            return -1;
        }

        if (!is_numeric($a) && is_numeric($b)) {
            return 1;
        }

        return is_numeric($a) && is_numeric($b)
            ? self::comparePrimitive(intval($a), intval($b))
            : self::comparePrimitive($a, $b);
    }

    /**
     * @param $a int|string The left side of the comparison.
     * @param $b int|string The right side of the comparison.
     * @return int -1 when $a < $b, 0 when $a == $b, 1 when $a > $b.
     */
    private static function comparePrimitive($a, $b)
    {
        if ($a != $b) {
            return $a < $b ? -1 : 1;
        }

        return 0;
    }
}
