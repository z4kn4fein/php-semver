<?php

namespace z4kn4fein\SemVer;

/**
 * Determines by which identifier the given Version should be incremented.
 *
 * @package z4kn4fein\SemVer
 */
class Inc
{
    /**
     * Indicates that the Version should be incremented by its MAJOR number.
     */
    const MAJOR = 0;
    /**
     * Indicates that the Version should be incremented by its MINOR number.
     */
    const MINOR = 1;
    /**
     * Indicates that the Version should be incremented by its PATCH number.
     */
    const PATCH = 2;
    /**
     * Indicates that the Version should be incremented by its PRE-RELEASE identifier.
     */
    const PRE_RELEASE = 3;
}
