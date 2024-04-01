<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer\Constraints;

use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Traits\Singles;
use z4kn4fein\SemVer\Version;

/**
 * @internal
 */
class Condition implements VersionComparator
{
    use Singles;

    private string $operator;
    private Version $version;

    public function __construct(string $operator, Version $version)
    {
        $this->operator = $operator;
        $this->version = $version;
    }

    /**
     * @return string the string representation of the condition
     */
    public function __toString(): string
    {
        return $this->operator.$this->version;
    }

    /**
     * @throws SemverException
     */
    public function isSatisfiedBy(Version $version): bool
    {
        return match ($this->operator) {
            Op::EQUAL => $version->isEqual($this->version),
            Op::NOT_EQUAL => !$version->isEqual($this->version),
            Op::LESS_THAN => $version->isLessThan($this->version),
            Op::LESS_THAN_OR_EQUAL, Op::LESS_THAN_OR_EQUAL2 => $version->isLessThanOrEqual($this->version),
            Op::GREATER_THAN => $version->isGreaterThan($this->version),
            Op::GREATER_THAN_OR_EQUAL, Op::GREATER_THAN_OR_EQUAL2 => $version->isGreaterThanOrEqual($this->version),
            default => throw new SemverException(sprintf('Invalid operator in condition %s', $this)),
        };
    }

    /**
     * @throws SemverException
     */
    public function opposite(): string
    {
        return match ($this->operator) {
            Op::EQUAL => Op::NOT_EQUAL.$this->version,
            Op::NOT_EQUAL => Op::EQUAL.$this->version,
            Op::LESS_THAN => Op::GREATER_THAN_OR_EQUAL.$this->version,
            Op::LESS_THAN_OR_EQUAL, Op::LESS_THAN_OR_EQUAL2 => Op::GREATER_THAN.$this->version,
            Op::GREATER_THAN => Op::LESS_THAN_OR_EQUAL.$this->version,
            Op::GREATER_THAN_OR_EQUAL, Op::GREATER_THAN_OR_EQUAL2 => Op::LESS_THAN.$this->version,
            default => throw new SemverException(sprintf('Invalid operator in condition %s', $this)),
        };
    }

    public static function greaterThanMin(): Condition
    {
        return self::single('greaterThanMin', function () {
            return new Condition(Op::GREATER_THAN_OR_EQUAL, Version::minVersion());
        });
    }
}
