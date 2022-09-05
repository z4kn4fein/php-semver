<?php

namespace z4kn4fein\SemVer;

use InvalidArgumentException;

/**
 * This class describes a semantic version and related operations.
 * @package z4kn4fein\SemVer
 */
class Version
{
    /** @var int */
    private $major;
    /** @var int */
    private $minor;
    /** @var int */
    private $patch;
    /** @var PreRelease|null */
    private $preRelease;
    /** @var string|null */
    private $buildMeta;

    /**
     * Constructs a semantic version.
     *
     * @param $major int The major version number.
     * @param $minor int The minor version number.
     * @param $patch int The patch version number.
     * @param PreRelease|null $preRelease The pre-release part.
     * @param string|null $buildMeta The build metadata.
     */
    private function __construct(int $major, int $minor, int $patch, PreRelease $preRelease = null, string $buildMeta = null)
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
    public function getMajor(): int
    {
        return $this->major;
    }

    /**
     * Returns the minor version number.
     *
     * @return int The minor version number.
     */
    public function getMinor(): int
    {
        return $this->minor;
    }

    /**
     * Returns the patch version number.
     *
     * @return int The patch version number.
     */
    public function getPatch(): int
    {
        return $this->patch;
    }

    /**
     * Returns the pre-release tag.
     *
     * @return null|string The pre-release part.
     */
    public function getPreRelease(): ?string
    {
        return $this->preRelease != null ? (string)$this->preRelease : null;
    }

    /**
     * Returns the build metadata.
     *
     * @return null|string The build metadata part.
     */
    public function getBuildMeta(): ?string
    {
        return $this->buildMeta;
    }

    /**
     * Returns true when the version has a pre-release tag.
     *
     * @return bool True when the version is a pre-release version.
     */
    public function isPreRelease(): bool
    {
        return $this->preRelease != null;
    }

    /**
     * Determines whether the version is considered stable or not.
     * Stable versions have a positive major number and no pre-release identifier.
     *
     * @return bool True when the version is a stable version.
     */
    public function isStable(): bool
    {
        return $this->major > 0 && !$this->isPreRelease();
    }

    /**
     * Produces the next major version.
     *
     * @param string|null $preRelease The pre-release part.
     * @return Version The next major version.
     * @throws SemverException When the pre-release tag is non-null and invalid.
     */
    public function getNextMajorVersion(string $preRelease = null): Version
    {
        return new Version($this->major + 1,
            0,
            0,
            !is_null($preRelease) ? PreRelease::parse($preRelease) : null
        );
    }

    /**
     * Produces the next minor version.
     *
     * @param string|null $preRelease The pre-release part.
     * @return Version The next minor version.
     * @throws SemverException When the pre-release tag is non-null and invalid.
     */
    public function getNextMinorVersion(string $preRelease = null): Version
    {
        return new Version($this->major,
            $this->minor + 1,
            0,
            !is_null($preRelease) ? PreRelease::parse($preRelease) : null
        );
    }

    /**
     * Produces the next patch version.
     *
     * @param string|null $preRelease The pre-release part.
     * @return Version The next patch version.
     * @throws SemverException When the pre-release tag is non-null and invalid.
     */
    public function getNextPatchVersion(string $preRelease = null): Version
    {
        return new Version($this->major,
            $this->minor,
            !$this->isPreRelease() || !is_null($preRelease) ? $this->patch + 1 : $this->patch,
            !is_null($preRelease) ? PreRelease::parse($preRelease) : null
        );
    }

    /**
     * Produces the next pre-release version.
     *
     * @param string|null $preRelease The pre-release part.
     * @return Version The next pre-release version.
     * @throws SemverException When the pre-release tag is non-null and invalid.
     */
    public function getNextPreReleaseVersion(string $preRelease = null): Version
    {
        $pre = PreRelease::createDefault();
        if (!empty($preRelease)) {
            $pre = $this->isPreRelease() && $this->preRelease->identity() == $preRelease
                ? $this->preRelease->increment()
                : PreRelease::parse($preRelease);
        } elseif ($this->isPreRelease()) {
            $pre = $this->preRelease->increment();
        }

        return new Version(
            $this->major,
            $this->minor,
            $this->isPreRelease() ? $this->patch : $this->patch + 1,
            $pre
        );
    }

    /**
     * Increases the version by its Inc::MAJOR, Inc::MINOR, Inc::PATCH, or Inc::PRE_RELEASE segment.
     * Returns a new version while the original remains unchanged.
     *
     * @param int $by Determines by which part the Version should be incremented.
     * @param string|null $preRelease The optional pre-release part.
     * @return Version The incremented version.
     * @throws SemverException When the pre-release tag is non-null and invalid.
     */
    public function inc(int $by, string $preRelease = null): Version
    {
        switch ($by) {
            case Inc::MAJOR: return $this->getNextMajorVersion($preRelease);
            case Inc::MINOR: return $this->getNextMinorVersion($preRelease);
            case Inc::PATCH: return $this->getNextPatchVersion($preRelease);
            case Inc::PRE_RELEASE: return $this->getNextPreReleaseVersion($preRelease);
            default: throw new InvalidArgumentException("Invalid `by` argument in inc() method");
        }
    }

    /**
     * Compares the version with the given one, returns true when the current is less than the other.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance < $v. Otherwise, false.
     */
    public function isLessThan(Version $v): bool
    {
        return self::compare($this, $v) < 0;
    }

    /**
     * Compares the version with the given one, returns true when the current is less than the other or equal.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance <= $v. Otherwise, false.
     */
    public function isLessThanOrEqual(Version $v): bool
    {
        return self::compare($this, $v) <= 0;
    }

    /**
     * Compares the version with the given one, returns true when the current is greater than the other.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance > $v. Otherwise, false.
     */
    public function isGreaterThan(Version $v): bool
    {
        return self::compare($this, $v) > 0;
    }

    /**
     * Compares the version with the given one, returns true when the current is greater than the other or equal.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance >= $v. Otherwise, false.
     */
    public function isGreaterThanOrEqual(Version $v): bool
    {
        return self::compare($this, $v) >= 0;
    }

    /**
     * Compares the version with the given one, returns true when they are equal.
     *
     * @param Version $v The version to compare.
     * @return bool True when instance == $v. Otherwise, false.
     */
    public function isEqual(Version $v): bool
    {
        return self::compare($this, $v) == 0;
    }

    /**
     * Constructs a copy of the version. The copied object's properties can be altered with the optional parameters.
     *
     * @param int|null $major The major version number.
     * @param int|null $minor The minor version number.
     * @param int|null $patch The patch version number.
     * @param string|null $preRelease The pre-release part.
     * @param string|null $buildMeta The build metadata.
     * @return Version The new version.
     * @throws SemverException When the version parts are invalid.
     */
    public function copy(int $major = null, int $minor = null, int $patch = null, string $preRelease = null, string $buildMeta = null): Version
    {
        return Version::create(
            $major == null ? $this->major : $major,
            $minor == null ? $this->minor : $minor,
            $patch == null ? $this->patch : $patch,
            $preRelease == null ? $this->preRelease : $preRelease,
            $buildMeta == null ? $this->buildMeta : $buildMeta
        );
    }

    /**
     * Produces a copy of the Version without the PRE-RELEASE and BUILD METADATA identities.
     *
     * @return Version The new version.
     */
    public function withoutSuffixes(): Version
    {
        return new Version($this->major, $this->minor, $this->patch);
    }

    /**
     * Parses a new version from the given version string.
     *
     * @param string $versionString The version string.
     * @return Version The parsed version.
     * @throws SemverException When the given version string is invalid.
     */
    public static function parse(string $versionString): Version
    {
        $versionString = trim($versionString);
        if (empty($versionString)) {
            throw new SemverException("versionString cannot be empty.");
        }

        if (!preg_match(Patterns::VERSION_REGEX, $versionString, $matches)) {
            throw new SemverException(sprintf("Invalid version: %s.", $versionString));
        }

        return new Version(
            intval($matches[1]),
            intval($matches[2]),
            intval($matches[3]),
            isset($matches[4]) && $matches[4] != ""
                ? PreRelease::parse($matches[4])
                : null,
            isset($matches[5]) && $matches[5] != ""
                ? $matches[5]
                : null
        );
    }

    /**
     * Creates a new version.
     *
     * @param int $major The major version number.
     * @param int $minor The minor version number.
     * @param int $patch The patch version number.
     * @param string|null $preRelease The pre-release part.
     * @param string|null $buildMeta The build metadata.
     * @return Version The new version.
     * @throws SemverException When the version parts are invalid.
     */
    public static function create(int $major, int $minor, int $patch, string $preRelease = null, string $buildMeta = null): Version
    {
        self::ensureValidState($major >= 0, "The major number must be >= 0.");
        self::ensureValidState($minor >= 0, "The minor number must be >= 0.");
        self::ensureValidState($patch >= 0, "The patch number must be >= 0.");

        return new Version(
            $major,
            $minor,
            $patch,
            $preRelease != null ? PreRelease::parse($preRelease) : null,
            $buildMeta
        );
    }

    /**
     * Compares two version strings and returns true when the first is less than the second.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 &lt; $v2. Otherwise, false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function lessThan(string $v1, string $v2): bool
    {
        $version1 = Version::parse($v1);
        $version2 = Version::parse($v2);

        return $version1->isLessThan($version2);
    }

    /**
     * Compares two version strings and returns true when the first is less than the second or equal.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 &lt;= $v2. Otherwise, false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function lessThanOrEqual(string $v1, string $v2): bool
    {
        $version1 = Version::parse($v1);
        $version2 = Version::parse($v2);

        return $version1->isLessThanOrEqual($version2);
    }

    /**
     * Compares two version strings and returns true when the first is greater than the second.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 &gt; $v2. Otherwise, false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function greaterThan(string $v1, string $v2): bool
    {
        $version1 = Version::parse($v1);
        $version2 = Version::parse($v2);

        return $version1->isGreaterThan($version2);
    }

    /**
     * Compares two version strings and returns true when the first is greater than the second or equal.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 &gt;= $v2. Otherwise, false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function greaterThanOrEqual(string $v1, string $v2): bool
    {
        $version1 = Version::parse($v1);
        $version2 = Version::parse($v2);

        return $version1->isGreaterThanOrEqual($version2);
    }

    /**
     * Compares two version strings and returns true when the first and second are equal.
     *
     * @param string $v1 The left side of the comparison.
     * @param string $v2 The right side of the comparison.
     * @return bool True when $v1 == $v2. Otherwise, false.
     * @throws SemverException When the version strings are invalid.
     */
    public static function equal(string $v1, string $v2): bool
    {
        $version1 = Version::parse($v1);
        $version2 = Version::parse($v2);

        return $version1->isEqual($version2);
    }

    /**
     * @param Version $v1 The left side of the comparison.
     * @param Version $v2 The right side of the comparison.
     * @return int -1 when $v1 < $v2, 0 when $v1 == $v2, 1 when $v1 > $v2.
     */
    private static function compare(Version $v1, Version $v2): int
    {
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

    /**
     * @param bool $condition The condition must be met.
     * @param string $message The exception message.
     * @throws SemverException When the condition failed.
     */
    private static function ensureValidState(bool $condition, string $message)
    {
        if (!$condition) {
            throw new SemverException($message);
        }
    }
}
