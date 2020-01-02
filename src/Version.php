<?php

namespace z4kn4fein\SemVer;

/**
 * Class Version This class describes a semantic version and related operations.
 * @package z4kn4fein\SemVer
 */
class Version
{
    // phpcs:ignore
    const VERSION_REGEX = "/^(?P<major>0|[1-9]\d*)\.(?P<minor>0|[1-9]\d*)\.(?P<patch>0|[1-9]\d*)(?:-(?P<prerelease>(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+(?P<buildmetadata>[0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/";

    /** @var int */
    private $major;
    /** @var int */
    private $minor;
    /** @var int */
    private $patch;
    /** @var null|PreRelease */
    private $preRelease;
    /** @var null|string */
    private $buildMeta;

    /**
     * Version constructor.
     * @param $versionString string The version string.
     * @throws VersionFormatException When the $versionString is invalid.
     */
    private function __construct($versionString)
    {
        $versionString = trim($versionString);
        if (empty($versionString)) {
            throw new VersionFormatException("versionString cannot be empty.");
        }

        if (!preg_match(self::VERSION_REGEX, $versionString, $matches)) {
            throw new VersionFormatException(sprintf("Invalid version: %s.", $versionString));
        }

        $this->major = intval($matches['major']);
        $this->minor = intval($matches['minor']);
        $this->patch = intval($matches['patch']);
        $this->preRelease = isset($matches['prerelease']) && $matches['prerelease'] != "" ? PreRelease::parse($matches['prerelease']) : null;
        $this->buildMeta = isset($matches['buildmetadata']) && $matches['buildmetadata'] != "" ? $matches['buildmetadata'] : null;
    }

    /**
     * @return string The string representation of the version.
     */
    public function __toString()
    {
        $result = implode('.', [$this->major, $this->minor, $this->patch]);
        $result .= isset($this->preRelease) ? '-' . $this->preRelease : '';
        $result .= isset($this->buildMeta) ? '+' . $this->buildMeta : '';
        return $result;
    }

    /**
     * @return int The major version number.
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @return int The minor version number.
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * @return int The patch version number.
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @return null|PreRelease The prerelease part.
     */
    public function getPreRelease()
    {
        return $this->preRelease;
    }

    /**
     * @return null|string The build metadata part.
     */
    public function getBuildMeta()
    {
        return $this->buildMeta;
    }

    /**
     * @return bool True when the version is a prerelease version.
     */
    public function isPreRelease()
    {
        return !empty($this->preRelease);
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

        $major = Utils::comparePrimitive($v1->getMajor(), $v2->getMajor());
        if ($major != 0) {
            return $major;
        }

        $minor = Utils::comparePrimitive($v1->getMinor(), $v2->getMinor());
        if ($minor != 0) {
            return $minor;
        }

        $patch = Utils::comparePrimitive($v1->getPatch(), $v2->getPatch());
        if ($patch != 0) {
            return $patch;
        }

        return self::compareByPreRelease($v1, $v2);
    }

    /**
     * @param $v1 Version The left side of the comparison.
     * @param $v2 Version The right side of the comparison.
     * @return int -1 when $v1 < $v2, 0 when $v1 == $v2, 1 when $v1 > $v2.
     * @throws VersionFormatException When the given prerelease values are invalid.
     */
    private static function compareByPreRelease($v1, $v2)
    {
        if ($v1->isPreRelease() && !$v2->isPreRelease()) {
            return -1;
        }

        if (!$v1->isPreRelease() && $v2->isPreRelease()) {
            return 1;
        }

        if ($v1->isPreRelease() && $v2->isPreRelease()) {
            return PreRelease::compare($v1->getPreRelease(), $v2->getPreRelease());
        }

        return 0;
    }
}
