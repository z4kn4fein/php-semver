<?php

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

    /** @var string */
    private $operator;
    /** @var Version */
    private $version;

    public function __construct(string $operator, Version $version)
    {
        $this->operator = $operator;
        $this->version = $version;
    }

    /**
     * @throws SemverException
     */
    public function isSatisfiedBy(Version $version): bool
    {
        switch ($this->operator) {
            case Op::EQUAL:
                return $version->isEqual($this->version);
            case Op::NOT_EQUAL:
                return !$version->isEqual($this->version);
            case Op::LESS_THAN:
                return $version->isLessThan($this->version);
            case Op::LESS_THAN_OR_EQUAL:
            case Op::LESS_THAN_OR_EQUAL2:
                return $version->isLessThanOrEqual($this->version);
            case Op::GREATER_THAN:
                return $version->isGreaterThan($this->version);
            case Op::GREATER_THAN_OR_EQUAL:
            case Op::GREATER_THAN_OR_EQUAL2:
                return $version->isGreaterThanOrEqual($this->version);
            default:
                throw new SemverException(sprintf("Invalid operator in condition %s", (string)$this));
        }
    }

    /**
     * @throws SemverException
     */
    public function opposite(): string
    {
        switch ($this->operator) {
            case Op::EQUAL:
                return Op::NOT_EQUAL . $this->version;
            case Op::NOT_EQUAL:
                return Op::EQUAL . $this->version;
            case Op::LESS_THAN:
                return Op::GREATER_THAN_OR_EQUAL . $this->version;
            case Op::LESS_THAN_OR_EQUAL:
            case Op::LESS_THAN_OR_EQUAL2:
                return Op::GREATER_THAN . $this->version;
            case Op::GREATER_THAN:
                return Op::LESS_THAN_OR_EQUAL . $this->version;
            case Op::GREATER_THAN_OR_EQUAL:
            case Op::GREATER_THAN_OR_EQUAL2:
                return Op::LESS_THAN . $this->version;
            default:
                throw new SemverException(sprintf("Invalid operator in condition %s", (string)$this));
        }
    }

    /**
     * @return string The string representation of the condition.
     */
    public function __toString(): string
    {
        return $this->operator . $this->version;
    }

    public static function greaterThanMin(): Condition
    {
        return self::single("greaterThanMin", function () {
            return new Condition(Op::GREATER_THAN_OR_EQUAL, Version::minVersion());
        });
    }
}
