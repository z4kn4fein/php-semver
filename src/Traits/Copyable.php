<?php

namespace z4kn4fein\SemVer\Traits;

use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

/**
 * This trait adds the copy method to Version.
 */
trait Copyable
{
    /**
     * Constructs a copy of the version. The copied object's properties can be altered with the optional parameters.
     *
     * @param null|int    $major      the major version number
     * @param null|int    $minor      the minor version number
     * @param null|int    $patch      the patch version number
     * @param null|string $preRelease the pre-release part
     * @param null|string $buildMeta  the build metadata
     *
     * @throws SemverException when the version parts are invalid
     *
     * @return Version the new version
     */
    public function copy(
        int $major = null,
        int $minor = null,
        int $patch = null,
        string $preRelease = null,
        string $buildMeta = null
    ): Version {
        return self::create(
            null == $major ? $this->major : $major,
            null == $minor ? $this->minor : $minor,
            null == $patch ? $this->patch : $patch,
            null === $preRelease
                ? null === $this->preRelease
                    ? null
                    : (string) $this->preRelease
                : $preRelease,
            null === $buildMeta ? $this->buildMeta : $buildMeta
        );
    }
}
