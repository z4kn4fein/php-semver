<?php

namespace z4kn4fein\SemVer\Constraints;

use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

/**
 * @internal
 */
class Range implements VersionComparator
{
    /** @var VersionComparator */
    private $start;
    /** @var VersionComparator */
    private $end;
    /** @var string */
    private $operator;

    public function __construct(VersionComparator $start, VersionComparator $end, string $operator)
    {
        $this->start = $start;
        $this->end = $end;
        $this->operator = $operator;
    }

    /**
     * @throws SemverException
     */
    public function isSatisfiedBy(Version $version): bool
    {
        switch ($this->operator) {
            case Op::EQUAL:
                return $this->start->isSatisfiedBy($version) && $this->end->isSatisfiedBy($version);
            case Op::NOT_EQUAL:
                return !$this->start->isSatisfiedBy($version) || !$this->end->isSatisfiedBy($version);
            case Op::LESS_THAN:
                return !$this->start->isSatisfiedBy($version) && $this->end->isSatisfiedBy($version);
            case Op::LESS_THAN_OR_EQUAL:
            case Op::LESS_THAN_OR_EQUAL2:
                return $this->end->isSatisfiedBy($version);
            case Op::GREATER_THAN:
                return $this->start->isSatisfiedBy($version) && !$this->end->isSatisfiedBy($version);
            case Op::GREATER_THAN_OR_EQUAL:
            case Op::GREATER_THAN_OR_EQUAL2:
                return $this->start->isSatisfiedBy($version);
            default:
                throw new SemverException(sprintf("Invalid operator in range %s", (string)$this));
        }
    }

    /**
     * @throws SemverException
     */
    public function opposite(): string
    {
        switch ($this->operator) {
            case Op::EQUAL:
                return $this->toStringByOp(Op::NOT_EQUAL);
            case Op::NOT_EQUAL:
                return $this->toStringByOp(Op::EQUAL);
            case Op::LESS_THAN:
                return $this->toStringByOp(Op::GREATER_THAN_OR_EQUAL);
            case Op::LESS_THAN_OR_EQUAL:
            case Op::LESS_THAN_OR_EQUAL2:
                return $this->toStringByOp(Op::GREATER_THAN);
            case Op::GREATER_THAN:
                return $this->toStringByOp(Op::LESS_THAN_OR_EQUAL);
            case Op::GREATER_THAN_OR_EQUAL:
            case Op::GREATER_THAN_OR_EQUAL2:
                return $this->toStringByOp(Op::LESS_THAN);
            default:
                throw new SemverException(sprintf("Invalid operator in range %s", (string)$this));
        }
    }

    /**
     * @return string The string representation of the range.
     */
    public function __toString(): string
    {
        return $this->toStringByOp($this->operator);
    }

    private function toStringByOp(string $op): string
    {
        switch ($op) {
            case Op::EQUAL:
                return sprintf("%s %s", (string)$this->start, (string)$this->end);
            case Op::NOT_EQUAL:
                return sprintf("%s || %s", $this->start->opposite(), $this->end->opposite());
            case Op::LESS_THAN:
                return $this->start->opposite();
            case Op::LESS_THAN_OR_EQUAL:
            case Op::LESS_THAN_OR_EQUAL2:
                return (string)$this->end;
            case Op::GREATER_THAN:
                return $this->end->opposite();
            case Op::GREATER_THAN_OR_EQUAL:
            case Op::GREATER_THAN_OR_EQUAL2:
                return (string)$this->start;
            default:
                return "";
        }
    }
}
