<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer;

use Exception;
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
 */
class Version
{
    use NextProducer;
    use Comparable;
    use Copyable;
    use Singles;
    use Validator;
    use Sortable;

    private int $major;
    private int $minor;
    private int $patch;
    private ?PreRelease $preRelease;
    private ?string $buildMeta;

    /**
     * Constructs a semantic version.
     *
     * @param                 $major      int The major version number
     * @param                 $minor      int The minor version number
     * @param                 $patch      int The patch version number
     * @param null|PreRelease $preRelease the pre-release part
     * @param null|string     $buildMeta  the build metadata
     */
    private function __construct(
        int $major,
        int $minor,
        int $patch,
        ?PreRelease $preRelease = null,
        ?string $buildMeta = null
    ) {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->buildMeta = $buildMeta;
    }

    /**
     * @return string the string representation of the version
     */
    public function __toString(): string
    {
        $result = implode('.', [$this->major, $this->minor, $this->patch]);
        $result .= isset($this->preRelease) ? '-'.$this->preRelease : '';
        $result .= isset($this->buildMeta) ? '+'.$this->buildMeta : '';

        return $result;
    }

    /**
     * Returns the major version number.
     *
     * @return int the major version number
     */
    public function getMajor(): int
    {
        return $this->major;
    }

    /**
     * Returns the minor version number.
     *
     * @return int the minor version number
     */
    public function getMinor(): int
    {
        return $this->minor;
    }

    /**
     * Returns the patch version number.
     *
     * @return int the patch version number
     */
    public function getPatch(): int
    {
        return $this->patch;
    }

    /**
     * Returns the pre-release tag.
     *
     * @return null|string the pre-release part
     */
    public function getPreRelease(): ?string
    {
        return null != $this->preRelease ? (string) $this->preRelease : null;
    }

    /**
     * Returns the build metadata.
     *
     * @return null|string the build metadata part
     */
    public function getBuildMeta(): ?string
    {
        return $this->buildMeta;
    }

    /**
     * Returns true when the version has a pre-release tag.
     *
     * @return bool true when the version is a pre-release version
     */
    public function isPreRelease(): bool
    {
        return null != $this->preRelease;
    }

    /**
     * Determines whether the version is considered stable or not.
     * Stable versions have a positive major number and no pre-release identifier.
     *
     * @return bool true when the version is a stable version
     */
    public function isStable(): bool
    {
        return $this->major > 0 && !$this->isPreRelease();
    }

    /**
     * Produces a copy of the Version without the PRE-RELEASE and BUILD METADATA identities.
     *
     * @return Version the new version
     */
    public function withoutSuffixes(): Version
    {
        return new Version($this->major, $this->minor, $this->patch);
    }

    /**
     * Determines whether the version satisfies the given Constraint or not.
     *
     * @param Constraint $constraint the constraint to satisfy
     *
     * @return bool true when the constraint is satisfied, otherwise false
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
        return self::single('min', function () {
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
     * @param string $versionString the version string
     * @param bool   $strict        enables or disables strict parsing
     *
     * @return null|Version the parsed version, or null if the parse fails
     */
    public static function parseOrNull(string $versionString, bool $strict = true): ?Version
    {
        try {
            return self::parse($versionString, $strict);
        } catch (Exception) {
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
     * @param string $versionString the version string
     * @param bool   $strict        enables or disables strict parsing
     *
     * @return Version the parsed version
     *
     * @throws SemverException when the given version string is invalid
     */
    public static function parse(string $versionString, bool $strict = true): Version
    {
        $versionString = trim($versionString);
        self::ensure('' !== $versionString, 'versionString cannot be empty.');
        self::ensure(
            (bool) preg_match(
                $strict ? Patterns::VERSION_REGEX : Patterns::LOOSE_VERSION_REGEX,
                $versionString,
                $matches
            ),
            sprintf('Invalid version: %s.', $versionString)
        );

        $matchedMajor = isset($matches[1]) && '' !== $matches[1];
        $matchedMinor = isset($matches[2]) && '' !== $matches[2];
        $matchedPatch = isset($matches[3]) && '' !== $matches[3];

        $preRelease = isset($matches[4]) && '' !== $matches[4]
            ? PreRelease::parse($matches[4])
            : null;
        $buildMeta = isset($matches[5]) && '' !== $matches[5]
            ? $matches[5]
            : null;

        if ($strict && $matchedMajor && $matchedMinor && $matchedPatch) {
            return new Version(
                intval($matches[1] ?? 0),
                intval($matches[2] ?? 0),
                intval($matches[3] ?? 0),
                $preRelease,
                $buildMeta
            );
        }
        if (!$strict && $matchedMajor) {
            return new Version(
                intval($matches[1] ?? 0),
                $matchedMinor ? intval($matches[2] ?? 0) : 0,
                $matchedPatch ? intval($matches[3] ?? 0) : 0,
                $preRelease,
                $buildMeta
            );
        }

        throw new SemverException(sprintf('Invalid version: %s.', $versionString));
    }

    /**
     * Constructs a semantic version from the given arguments following the pattern:
     * <[major]>.<[minor]>.<[patch]>-<[preRelease]>+<[buildMetadata]>.
     *
     * @param int         $major      the major version number
     * @param int         $minor      the minor version number
     * @param int         $patch      the patch version number
     * @param null|string $preRelease the pre-release part
     * @param null|string $buildMeta  the build metadata
     *
     * @return Version the new version
     *
     * @throws SemverException when the version parts are invalid
     */
    public static function create(
        int $major,
        int $minor,
        int $patch,
        ?string $preRelease = null,
        ?string $buildMeta = null
    ): Version {
        self::ensure($major >= 0, 'The major number must be >= 0.');
        self::ensure($minor >= 0, 'The minor number must be >= 0.');
        self::ensure($patch >= 0, 'The patch number must be >= 0.');

        return new Version(
            $major,
            $minor,
            $patch,
            null !== $preRelease ? PreRelease::parse($preRelease) : null,
            $buildMeta
        );
    }

    /**
     * Determines whether a Version satisfies a Constraint or not.
     *
     * @param string $versionString    the version to check
     * @param string $constraintString the constraint to satisfy
     *
     * @return bool true when the given version satisfies the given constraint, otherwise false
     *
     * @throws SemverException when the version string or the constraint string is invalid
     */
    public static function satisfies(string $versionString, string $constraintString): bool
    {
        $version = self::parse($versionString);
        $constraint = Constraint::parse($constraintString);

        return $version->isSatisfying($constraint);
    }
}
