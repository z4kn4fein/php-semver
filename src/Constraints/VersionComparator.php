<?php

namespace z4kn4fein\SemVer\Constraints;

use z4kn4fein\SemVer\Version;

interface VersionComparator
{
    public function isSatisfiedBy(Version $version): bool;
    public function opposite(): string;
    public function __toString(): string;
}
