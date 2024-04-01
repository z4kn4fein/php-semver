<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer\Traits;

/**
 * @internal
 */
trait Singles
{
    /** @var array<mixed> */
    private static array $singles = [];

    /**
     * This method gets a single instance for a particular key.
     *
     * @param string   $key     the key for the instance
     * @param callable $factory the factory function used when an instance doesn't exist for the key
     *
     * @return mixed the instance
     */
    private static function single(string $key, callable $factory): mixed
    {
        if (!isset(self::$singles[$key])) {
            self::$singles[$key] = $factory();
        }

        return self::$singles[$key];
    }
}
