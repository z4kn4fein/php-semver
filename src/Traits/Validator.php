<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer\Traits;

use z4kn4fein\SemVer\SemverException;

/**
 * @internal
 */
trait Validator
{
    /**
     * @param bool   $condition the condition to evaluate
     * @param string $message   the exception message when the condition evaluates to false
     *
     * @throws SemverException when the condition evaluates to false
     */
    private static function ensure(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new SemverException($message);
        }
    }
}
