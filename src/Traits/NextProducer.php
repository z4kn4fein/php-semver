<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer\Traits;

use z4kn4fein\SemVer\Inc;
use z4kn4fein\SemVer\PreRelease;
use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

/**
 * This trait used to produce incremented Version.
 */
trait NextProducer
{
    /**
     * Produces the next major version.
     *
     * @param null|string $preRelease the pre-release part
     *
     * @return Version the next major version
     *
     * @throws SemverException when the pre-release tag is non-null and invalid
     */
    public function getNextMajorVersion(?string $preRelease = null): Version
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
     * @param null|string $preRelease the pre-release part
     *
     * @return Version the next minor version
     *
     * @throws SemverException when the pre-release tag is non-null and invalid
     */
    public function getNextMinorVersion(?string $preRelease = null): Version
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
     * @param null|string $preRelease the pre-release part
     *
     * @return Version the next patch version
     *
     * @throws SemverException when the pre-release tag is non-null and invalid
     */
    public function getNextPatchVersion(?string $preRelease = null): Version
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
     * @param null|string $preRelease the pre-release part
     *
     * @return Version the next pre-release version
     *
     * @throws SemverException when the pre-release tag is non-null and invalid
     */
    public function getNextPreReleaseVersion(?string $preRelease = null): Version
    {
        $pre = PreRelease::default();
        if (!empty($preRelease)) {
            $pre = null != $this->preRelease && $this->preRelease->identity() === $preRelease
                ? $this->preRelease->increment()
                : PreRelease::parse($preRelease);
        } elseif (null != $this->preRelease) {
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
     * @param int         $by         determines by which part the Version should be incremented
     * @param null|string $preRelease the optional pre-release part
     *
     * @return Version the incremented version
     *
     * @throws SemverException when the pre-release tag is non-null and invalid
     */
    public function inc(int $by, ?string $preRelease = null): Version
    {
        return match ($by) {
            Inc::MAJOR => $this->getNextMajorVersion($preRelease),
            Inc::MINOR => $this->getNextMinorVersion($preRelease),
            Inc::PATCH => $this->getNextPatchVersion($preRelease),
            Inc::PRE_RELEASE => $this->getNextPreReleaseVersion($preRelease),
            default => throw new SemverException('Invalid `by` argument in inc() method'),
        };
    }
}
