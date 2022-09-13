<?php

namespace z4kn4fein\SemVer\Traits;

use z4kn4fein\SemVer\SemverException;

/**
 * @internal
 */
trait Validator
{
    /**
     * @param bool $condition The condition to evaluate.
     * @param string $message The exception message when the condition evaluates to false.
     * @throws SemverException When the condition evaluates to false.
     */
    private static function ensure(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new SemverException($message);
        }
    }
}
