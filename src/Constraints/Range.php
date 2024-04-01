<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer\Constraints;

use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

/**
 * @internal
 */
class Range implements VersionComparator
{
    private VersionComparator $start;
    private VersionComparator $end;
    private string $operator;

    public function __construct(VersionComparator $start, VersionComparator $end, string $operator)
    {
        $this->start = $start;
        $this->end = $end;
        $this->operator = $operator;
    }

    /**
     * @return string the string representation of the range
     */
    public function __toString(): string
    {
        return $this->toStringByOp($this->operator);
    }

    /**
     * @throws SemverException
     */
    public function isSatisfiedBy(Version $version): bool
    {
        return match ($this->operator) {
            Op::EQUAL => $this->start->isSatisfiedBy($version) && $this->end->isSatisfiedBy($version),
            Op::NOT_EQUAL => !$this->start->isSatisfiedBy($version) || !$this->end->isSatisfiedBy($version),
            Op::LESS_THAN => !$this->start->isSatisfiedBy($version) && $this->end->isSatisfiedBy($version),
            Op::LESS_THAN_OR_EQUAL, Op::LESS_THAN_OR_EQUAL2 => $this->end->isSatisfiedBy($version),
            Op::GREATER_THAN => $this->start->isSatisfiedBy($version) && !$this->end->isSatisfiedBy($version),
            Op::GREATER_THAN_OR_EQUAL, Op::GREATER_THAN_OR_EQUAL2 => $this->start->isSatisfiedBy($version),
            default => throw new SemverException(sprintf('Invalid operator in range %s', $this)),
        };
    }

    /**
     * @throws SemverException
     */
    public function opposite(): string
    {
        return match ($this->operator) {
            Op::EQUAL => $this->toStringByOp(Op::NOT_EQUAL),
            Op::NOT_EQUAL => $this->toStringByOp(Op::EQUAL),
            Op::LESS_THAN => $this->toStringByOp(Op::GREATER_THAN_OR_EQUAL),
            Op::LESS_THAN_OR_EQUAL, Op::LESS_THAN_OR_EQUAL2 => $this->toStringByOp(Op::GREATER_THAN),
            Op::GREATER_THAN => $this->toStringByOp(Op::LESS_THAN_OR_EQUAL),
            Op::GREATER_THAN_OR_EQUAL, Op::GREATER_THAN_OR_EQUAL2 => $this->toStringByOp(Op::LESS_THAN),
            default => throw new SemverException(sprintf('Invalid operator in range %s', $this)),
        };
    }

    private function toStringByOp(string $op): string
    {
        return match ($op) {
            Op::EQUAL => sprintf('%s %s', (string) $this->start, (string) $this->end),
            Op::NOT_EQUAL => sprintf('%s || %s', $this->start->opposite(), $this->end->opposite()),
            Op::LESS_THAN => $this->start->opposite(),
            Op::LESS_THAN_OR_EQUAL, Op::LESS_THAN_OR_EQUAL2 => (string) $this->end,
            Op::GREATER_THAN => $this->end->opposite(),
            Op::GREATER_THAN_OR_EQUAL, Op::GREATER_THAN_OR_EQUAL2 => (string) $this->start,
            default => '',
        };
    }
}
