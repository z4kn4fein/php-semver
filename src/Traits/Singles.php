<?php

namespace z4kn4fein\SemVer\Traits;

/**
 * @internal
 */
trait Singles
{
    /** @var array */
    private static $singles = [];

    /**
     * This method gets a single instance for a particular key.
     *
     * @param string $key The key for the instance.
     * @param callable $factory The factory function used when an instance doesn't exist for the key.
     * @return mixed The instance.
     */
    private static function single(string $key, callable $factory)
    {
        if (!isset(self::$singles[$key])) {
            self::$singles[$key] = $factory();
        }
        return self::$singles[$key];
    }
}
