<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer\Traits;

/**
 * @internal
 */
trait Iterator
{
    /**
     * Determines whether a condition is true for at least one item of a collection.
     *
     * @param mixed[]  $iterable  the collection to check
     * @param callable $predicate the condition
     *
     * @return bool true when the condition is true for at least one item, otherwise false
     */
    private static function any(array $iterable, callable $predicate): bool
    {
        foreach ($iterable as $value) {
            if ($predicate($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines whether a condition is true for each item of a collection.
     *
     * @param mixed[]  $iterable  the collection to check
     * @param callable $predicate the condition
     *
     * @return bool true when the condition is true for each item, otherwise false
     */
    private static function all(array $iterable, callable $predicate): bool
    {
        foreach ($iterable as $value) {
            if (!$predicate($value)) {
                return false;
            }
        }

        return true;
    }
}
