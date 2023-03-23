<?php

namespace z4kn4fein\SemVer\Traits;

use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

/**
 * This trait adds the copy method to Version.
 *
 * @package z4kn4fein\SemVer\Traits
 */
trait Copyable
{
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
    public function copy(
        int $major = null,
        int $minor = null,
        int $patch = null,
        string $preRelease = null,
        string $buildMeta = null
    ): Version {
        return self::create(
            $major == null ? $this->major : $major,
            $minor == null ? $this->minor : $minor,
            $patch == null ? $this->patch : $patch,
            $preRelease === null
                ? $this->preRelease === null
                    ? null
                    : (string)$this->preRelease
                : $preRelease,
            $buildMeta === null ? $this->buildMeta : $buildMeta
        );
    }
}
