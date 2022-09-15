<?php

namespace z4kn4fein\SemVer;

use z4kn4fein\SemVer\Constraints\Constraint;
use z4kn4fein\SemVer\Traits\Comparable;
use z4kn4fein\SemVer\Traits\Copyable;
use z4kn4fein\SemVer\Traits\NextProducer;
use z4kn4fein\SemVer\Traits\Singles;
use z4kn4fein\SemVer\Traits\Sortable;
use z4kn4fein\SemVer\Traits\Validator;

/**
 * This class describes a semantic version and related operations following the semver 2.0.0 specification.
 * Instances of this class are immutable.
 *
 * @package z4kn4fein\SemVer
 */
class Version
{
    use NextProducer;
    use Comparable;
    use Copyable;
    use Singles;
    use Validator;
    use Sortable;

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
    private function __construct(
        int $major,
        int $minor,
        int $patch,
        PreRelease $preRelease = null,
        string $buildMeta = null
    ) {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->buildMeta = $buildMeta;
    }

    /**
     * @return string The string representation of the version.
     */
    public function __toString(): string
    {
        $result = implode(".", [$this->major, $this->minor, $this->patch]);
        $result .= isset($this->preRelease) ? "-" . $this->preRelease : "";
        $result .= isset($this->buildMeta) ? "+" . $this->buildMeta : "";
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
     * Produces a copy of the Version without the PRE-RELEASE and BUILD METADATA identities.
     *
     * @return Version The new version.
     */
    public function withoutSuffixes(): Version
    {
        return new Version($this->major, $this->minor, $this->patch);
    }

    /**
     * Determines whether the version satisfies the given Constraint or not.
     *
     * @param Constraint $constraint The constraint to satisfy.
     * @return bool True when the constraint is satisfied, otherwise false.
     */
    public function isSatisfying(Constraint $constraint): bool
    {
        return $constraint->isSatisfiedBy($this);
    }

    /**
     * @return Version The 0.0.0 semantic version.
     */
    public static function minVersion(): Version
    {
        return self::single("min", function () {
            return new Version(0, 0, 0);
        });
    }

    /**
     * Parses the given string as a Version and returns the result or null
     * if the string is not a valid representation of a semantic version.
     *
     * Strict mode is on by default, which means partial versions (e.g. '1.0' or '1') and versions with 'v' prefix
     * are considered invalid. This behaviour can be turned off by setting the strict parameter to false.
     *
     * @param string $versionString The version string.
     * @param bool $strict Enables or disables strict parsing.
     * @return Version|null The parsed version, or null if the parse fails.
     */
    public static function parseOrNull(string $versionString, bool $strict = true): ?Version
    {
        try {
            return self::parse($versionString, $strict);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Parses the given string as a Version and returns the result or throws a SemverException
     * if the string is not a valid representation of a semantic version.
     *
     * Strict mode is on by default, which means partial versions (e.g. '1.0' or '1') and versions with 'v' prefix
     * are considered invalid. This behaviour can be turned off by setting the strict parameter to false.
     *
     * @param string $versionString The version string.
     * @param bool $strict Enables or disables strict parsing.
     * @return Version The parsed version.
     * @throws SemverException When the given version string is invalid.
     */
    public static function parse(string $versionString, bool $strict = true): Version
    {
        $versionString = trim($versionString);
        self::ensure($versionString !== "", "versionString cannot be empty.");
        self::ensure(
            (bool)preg_match(
                $strict ? Patterns::VERSION_REGEX : Patterns::LOOSE_VERSION_REGEX,
                $versionString,
                $matches
            ),
            sprintf("Invalid version: %s.", $versionString)
        );

        $matchedMajor = isset($matches[1]) && $matches[1] !== "";
        $matchedMinor = isset($matches[2]) && $matches[2] !== "";
        $matchedPatch = isset($matches[3]) && $matches[3] !== "";

        $preRelease = isset($matches[4]) && $matches[4] !== ""
            ? PreRelease::parse($matches[4])
            : null;
        $buildMeta = isset($matches[5]) && $matches[5] !== ""
            ? $matches[5]
            : null;

        if ($strict && $matchedMajor && $matchedMinor && $matchedPatch) {
            return new Version(
                intval($matches[1]),
                intval($matches[2]),
                intval($matches[3]),
                $preRelease,
                $buildMeta
            );
        } elseif (!$strict && $matchedMajor) {
            return new Version(
                intval($matches[1]),
                $matchedMinor ? intval($matches[2]) : 0,
                $matchedPatch ? intval($matches[3]) : 0,
                $preRelease,
                $buildMeta
            );
        } else {
            throw new SemverException(sprintf("Invalid version: %s.", $versionString));
        }
    }

    /**
     * Constructs a semantic version from the given arguments following the pattern:
     * <[major]>.<[minor]>.<[patch]>-<[preRelease]>+<[buildMetadata]>
     *
     * @param int $major The major version number.
     * @param int $minor The minor version number.
     * @param int $patch The patch version number.
     * @param string|null $preRelease The pre-release part.
     * @param string|null $buildMeta The build metadata.
     * @return Version The new version.
     * @throws SemverException When the version parts are invalid.
     */
    public static function create(
        int $major,
        int $minor,
        int $patch,
        string $preRelease = null,
        string $buildMeta = null
    ): Version {
        self::ensure($major >= 0, "The major number must be >= 0.");
        self::ensure($minor >= 0, "The minor number must be >= 0.");
        self::ensure($patch >= 0, "The patch number must be >= 0.");

        return new Version(
            $major,
            $minor,
            $patch,
            $preRelease !== null ? PreRelease::parse($preRelease) : null,
            $buildMeta
        );
    }

    /**
     * Determines whether a Version satisfies a Constraint or not.
     *
     * @param string $versionString The version to check.
     * @param string $constraintString The constraint to satisfy.
     * @return bool True when the given version satisfies the given constraint, otherwise false.
     * @throws SemverException When the version string or the constraint string is invalid.
     */
    public static function satisfies(string $versionString, string $constraintString): bool
    {
        $version = self::parse($versionString);
        $constraint = Constraint::parse($constraintString);

        return $version->isSatisfying($constraint);
    }
}
