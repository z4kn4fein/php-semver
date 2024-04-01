<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer\Constraints;

use z4kn4fein\SemVer\Version;

/**
 * @internal
 */
interface VersionComparator
{
    public function __toString(): string;

    public function isSatisfiedBy(Version $version): bool;

    public function opposite(): string;
}
