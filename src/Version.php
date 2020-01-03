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
     *
     * @param $major int The major version number.
     * @param $minor int The minor version number.
     * @param $patch int The patch version number.
     * @param null|PreRelease $preRelease The prerelease part.
     * @param null|string $buildMeta The build metadata.
     */
    private function __construct($major, $minor, $patch, $preRelease = null, $buildMeta = null)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->buildMeta = $buildMeta;
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
     * Returns the major version number.
     *
     * @return int The major version number.
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * Returns the minor version number.
     *
     * @return int The minor version number.
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * Returns the patch version number.
     *
     * @return int The patch version number.
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * Returns the prerelease tag.
     *
     * @return null|PreRelease The prerelease part.
     */
    public function getPreRelease()
    {
        return $this->preRelease;
    }

    /**
     * Returns the build metadata.
     *
     * @return null|string The build metadata part.
     */
    public function getBuildMeta()
    {
        return $this->buildMeta;
    }

    /**
     * Returns true when the version has a prerelease tag.
     *
     * @return bool True when the version is a prerelease version.
     */
    public function isPreRelease()
    {
        return $this->preRelease != null;
    }

    /**
     * Produces the next major version.
     *
     * @return Version The next major version.
     */
    public function getNextMajorVersion()
    {
        return new Version($this->major + 1, 0, 0);
    }

    /**
     * Produces the next minor version.
     *
     * @return Version The next minor version.
     */
    public function getNextMinorVersion()
    {
        return new Version($this->major, $this->minor + 1, 0);
    }

    /**
     * Produces the next patch version.
     *
     * @return Version The next patch version.
     */
    public function getNextPatchVersion()
    {
        return new Version($this->major, $this->minor, $this->isPreRelease() ? $this->patch : $this->patch + 1);
    }

    /**
     * Produces the next prerelease version.
     *
     * @return Version The next prerelease version.
     */
    public function getNextPreReleaseVersion()
    {
        return new Version($this->major,
            $this->minor,
            $this->isPreRelease() ? $this->patch : $this->patch + 1,
            $this->isPreRelease() ? $this->preRelease->increment() : PreRelease::createDefault());
    }

    /**
     * Compares the version with the given one, returns true when the current is less than the other.
     *
     * @param string|Version $v The the version to compare.
     * @return bool True when instance < $v. Otherwise false.
     * @throws VersionFormatException When the given version is invalid.
     */
    public function isLessThan($v)
    {
        return self::lessThan($this, $v);
    }

    /**
     * Compares the version with the given one, returns true when the current is less than the other or equal.
     *
     * @param string|Version $v The the version to compare.
     * @return bool True when instance <= $v. Otherwise false.
     * @throws VersionFormatException When the given version is invalid.
     */
    public function isLessThanOrEqual($v)
    {
        return self::lessThanOrEqual($this, $v);
    }

    /**
     * Compares the version with the given one, returns true when the current is greater than the other.
     *
     * @param string|Version $v The the version to compare.
     * @return bool True when instance > $v. Otherwise false.
     * @throws VersionFormatException When the given version is invalid.
     */
    public function isGreaterThan($v)
    {
        return self::greaterThan($this, $v);
    }

    /**
     * Compares the version with the given one, returns true when the current is greater than the other or equal.
     *
     * @param string|Version $v The the version to compare.
     * @return bool True when instance >= $v. Otherwise false.
     * @throws VersionFormatException When the given version is invalid.
     */
    public function isGreaterThanOrEqual($v)
    {
        return self::greaterThanOrEqual($this, $v);
    }

    /**
     * Compares the version with the given one, returns true when they are equal.
     *
     * @param string|Version $v The the version to compare.
     * @return bool True when instance == $v. Otherwise false.
     * @throws VersionFormatException When the given version is invalid.
     */
    public function isEqual($v)
    {
        return self::equal($this, $v);
    }

    /**
     * Parses a new version from the given version string.
     *
     * @param string $versionString The version string.
     * @return Version The parsed version.
     * @throws VersionFormatException When the given version string is invalid.
     */
    public static function parse($versionString)
    {
        $versionString = trim($versionString);
        if (empty($versionString)) {
            throw new VersionFormatException("versionString cannot be empty.");
        }

        if (!preg_match(self::VERSION_REGEX, $versionString, $matches)) {
            throw new VersionFormatException(sprintf("Invalid version: %s.", $versionString));
        }

        return new Version(intval($matches['major']),
            intval($matches['minor']),
            intval($matches['patch']),
            isset($matches['prerelease']) && $matches['prerelease'] != ""
                ? PreRelease::parse($matches['prerelease'])
                : null,
            isset($matches['buildmetadata']) && $matches['buildmetadata'] != ""
                ? $matches['buildmetadata']
                : null);
    }

    /**
     * Creates a new version.
     *
     * @param $major int The major version number.
     * @param $minor int The minor version number.
     * @param $patch int The patch version number.
     * @param null|string $preRelease The prerelease part.
     * @param null|string $buildMeta The build metadata.
     * @return Version The new version.
     * @throws VersionFormatException When the version parts are invalid.
     */
    public static function create($major, $minor, $patch, $preRelease = null, $buildMeta = null)
    {
        self::ensureValidState($major >= 0, "The major number must be >= 0.");
        self::ensureValidState($minor >= 0, "The minor number must be >= 0.");
        self::ensureValidState($patch >= 0, "The patch number must be >= 0.");

        return new Version($major,
            $minor,
            $patch,
            $preRelease != null ? PreRelease::parse($preRelease) : null,
            $buildMeta);
    }

    /**
     * Compares two versions and returns true when the first is less than the second.
     *
     * @param string|Version $v1 The left side of the comparison.
     * @param string|Version $v2 The right side of the comparison.
     * @return bool True when $v1 < $v2. Otherwise false.
     * @throws VersionFormatException When the given versions are invalid.
     */
    public static function lessThan($v1, $v2)
    {
        return self::compare($v1, $v2) < 0;
    }

    /**
     * Compares two versions and returns true when the first is less than the second or equal.
     *
     * @param string|Version $v1 The left side of the comparison.
     * @param string|Version $v2 The right side of the comparison.
     * @return bool True when $v1 <= $v2. Otherwise false.
     * @throws VersionFormatException When the given versions are invalid.
     */
    public static function lessThanOrEqual($v1, $v2)
    {
        return self::compare($v1, $v2) <= 0;
    }

    /**
     * Compares two versions and returns true when the first is greater than the second.
     *
     * @param string|Version $v1 The left side of the comparison.
     * @param string|Version $v2 The right side of the comparison.
     * @return bool True when $v1 > $v2. Otherwise false.
     * @throws VersionFormatException When the given versions are invalid.
     */
    public static function greaterThan($v1, $v2)
    {
        return self::compare($v1, $v2) > 0;
    }

    /**
     * Compares two versions and returns true when the first is greater than the second or equal.
     *
     * @param string|Version $v1 The left side of the comparison.
     * @param string|Version $v2 The right side of the comparison.
     * @return bool True when $v1 >= $v2. Otherwise false.
     * @throws VersionFormatException When the given versions are invalid.
     */
    public static function greaterThanOrEqual($v1, $v2)
    {
        return self::compare($v1, $v2) >= 0;
    }

    /**
     * Compares two versions and returns true when the first and second are equal.
     *
     * @param string|Version $v1 The left side of the comparison.
     * @param string|Version $v2 The right side of the comparison.
     * @return bool True when $v1 == $v2. Otherwise false.
     * @throws VersionFormatException When the given versions are invalid.
     */
    public static function equal($v1, $v2)
    {
        return self::compare($v1, $v2) == 0;
    }

    /**
     * @param string|Version $v1 The left side of the comparison.
     * @param string|Version $v2 The right side of the comparison.
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
     * @param string|Version $v1 The left side of the comparison.
     * @param string|Version $v2 The right side of the comparison.
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

    /**
     * @param bool $condition The condition must be met.
     * @param string $message The exception message.
     * @throws VersionFormatException When the condition failed.
     */
    private static function ensureValidState($condition, $message)
    {
        if (!$condition) {
            throw new VersionFormatException($message);
        }
    }
}
