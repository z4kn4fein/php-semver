<?php

namespace z4kn4fein\SemVer\Traits;

use z4kn4fein\SemVer\Inc;
use z4kn4fein\SemVer\PreRelease;
use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

/**
 * This trait used to produce incremented Version.
 *
 * @package z4kn4fein\SemVer\Traits
 */
trait NextProducer
{
    /**
     * Produces the next major version.
     *
     * @param string|null $preRelease The pre-release part.
     * @return Version The next major version.
     * @throws SemverException When the pre-release tag is non-null and invalid.
     */
    public function getNextMajorVersion(string $preRelease = null): Version
    {
        return new Version(
            $this->major + 1,
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
        return new Version(
            $this->major,
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
        return new Version(
            $this->major,
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
        $pre = PreRelease::default();
        if (!empty($preRelease)) {
            $pre = $this->preRelease != null && $this->preRelease->identity() === $preRelease
                ? $this->preRelease->increment()
                : PreRelease::parse($preRelease);
        } elseif ($this->preRelease != null) {
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
            case Inc::MAJOR:
                return $this->getNextMajorVersion($preRelease);
            case Inc::MINOR:
                return $this->getNextMinorVersion($preRelease);
            case Inc::PATCH:
                return $this->getNextPatchVersion($preRelease);
            case Inc::PRE_RELEASE:
                return $this->getNextPreReleaseVersion($preRelease);
            default:
                throw new SemverException("Invalid `by` argument in inc() method");
        }
    }
}
