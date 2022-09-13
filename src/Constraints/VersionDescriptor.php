<?php

namespace z4kn4fein\SemVer\Constraints;

use z4kn4fein\SemVer\Patterns;
use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Traits\Validator;
use z4kn4fein\SemVer\Version;

/**
 * @internal
 */
class VersionDescriptor
{
    use Validator;

    /** @var string */
    private $major;
    /** @var null|string */
    private $minor;
    /** @var null|string */
    private $patch;
    /** @var null|string */
    private $preRelease;
    /** @var null|string */
    private $buildMeta;

    /** @var bool */
    private $isMajorWildcard;
    /** @var bool */
    private $isMinorWildCard;
    /** @var bool */
    private $isPatchWildCard;
    /** @var bool */
    private $isWildCard;

    /**
     * @param string $major
     * @param string|null $minor
     * @param string|null $patch
     * @param string|null $preRelease
     * @param string|null $buildMeta
     */
    public function __construct(string $major, ?string $minor, ?string $patch, ?string $preRelease, ?string $buildMeta)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->buildMeta = $buildMeta;

        $this->isMajorWildcard = Patterns::isWildcard($major);
        $this->isMinorWildCard = is_null($minor) || Patterns::isWildcard($minor);
        $this->isPatchWildCard = is_null($patch) || Patterns::isWildcard($patch);
        $this->isWildCard = $this->isMajorWildcard || $this->isMinorWildCard || $this->isPatchWildCard;
    }

    /**
     * @return string The string representation of the descriptor.
     */
    public function __toString(): string
    {
        $result = $this->major;
        $result .= isset($this->minor) ? "." . $this->minor : "";
        $result .= isset($this->patch) ? "." . $this->patch : "";
        $result .= isset($this->preRelease) ? "-" . $this->preRelease : "";
        $result .= isset($this->buildMeta) ? "+" . $this->buildMeta : "";
        return $result;
    }

    /**
     * @throws SemverException
     */
    public function getIntMajor(): int
    {
        self::ensure(is_numeric($this->major), sprintf("Invalid MAJOR number in: %s", (string)$this));
        return intval($this->major);
    }

    /**
     * @throws SemverException
     */
    public function getIntMinor(): int
    {
        self::ensure(is_numeric($this->minor), sprintf("Invalid MINOR number in: %s", (string)$this));
        return intval($this->minor);
    }

    /**
     * @throws SemverException
     */
    public function getIntPatch(): int
    {
        self::ensure(is_numeric($this->patch), sprintf("Invalid PATCH number in: %s", (string)$this));
        return intval($this->patch);
    }

    /**
     * @throws SemverException
     */
    public function fromOperator(string $operator): VersionComparator
    {
        if (in_array($operator, Patterns::COMPARISON_OPERATORS, true) || $operator === "") {
            return $this->toComparator($operator);
        }

        if (in_array($operator, Patterns::TILDE_OPERATORS, true)) {
            return $this->fromTilde();
        }
        if ($operator === Patterns::CARET_OPERATOR) {
            return $this->fromCaret();
        }

        throw new SemverException(sprintf("Invalid constraint operator: %s in %s", $operator, (string)$this));
    }

    /**
     * @throws SemverException
     */
    private function fromTilde(): VersionComparator
    {
        if ($this->isWildCard) {
            return $this->toComparator();
        }
        $version = Version::create(
            $this->getIntMajor(),
            $this->getIntMinor(),
            $this->getIntPatch(),
            $this->preRelease,
            $this->buildMeta
        );

        return new Range(
            new Condition(Op::GREATER_THAN_OR_EQUAL, $version),
            new Condition(Op::LESS_THAN, $version->getNextMinorVersion("")),
            Op::EQUAL
        );
    }

    /**
     * @throws SemverException
     */
    private function fromCaret(): VersionComparator
    {
        if ($this->isMajorWildcard) {
            return Condition::greaterThanMin();
        }
        if ($this->isMinorWildCard) {
            return $this->fromMinorCaret();
        }
        if ($this->isPatchWildCard) {
            return $this->fromPatchCaret();
        }

        $version = Version::create(
            $this->getIntMajor(),
            $this->getIntMinor(),
            $this->getIntPatch(),
            $this->preRelease,
            $this->buildMeta
        );

        $endVersion = Version::create(0, 0, 1, "");
        if ($this->major !== "0") {
            $endVersion = $version->getNextMajorVersion("");
        } elseif ($this->minor !== "0") {
            $endVersion = $version->getNextMinorVersion("");
        } elseif ($this->patch !== "0") {
            $endVersion = $version->getNextPatchVersion("");
        }

        return new Range(
            new Condition(Op::GREATER_THAN_OR_EQUAL, $version),
            new Condition(Op::LESS_THAN, $endVersion),
            Op::EQUAL
        );
    }

    /**
     * @throws SemverException
     */
    private function fromMinorCaret(): VersionComparator
    {
        if ($this->major === "0") {
            return new Range(
                Condition::greaterThanMin(),
                new Condition(Op::LESS_THAN, Version::create(1, 0, 0, "")),
                Op::EQUAL
            );
        }

        return $this->toComparator();
    }

    /**
     * @throws SemverException
     */
    private function fromPatchCaret(): VersionComparator
    {
        if ($this->major === "0" && $this->minor === "0") {
            return new Range(
                Condition::greaterThanMin(),
                new Condition(Op::LESS_THAN, Version::create(0, 1, 0, "")),
                Op::EQUAL
            );
        }

        if ($this->major !== '0') {
            $version = Version::create($this->getIntMajor(), $this->getIntMinor(), 0);
            return new Range(
                new Condition(Op::GREATER_THAN_OR_EQUAL, $version),
                new Condition(Op::LESS_THAN, $version->getNextMajorVersion("")),
                Op::EQUAL
            );
        }

        return $this->toComparator();
    }

    /**
     * @throws SemverException
     */
    public function toComparator(string $operator = Op::EQUAL): VersionComparator
    {
        if ($this->isMajorWildcard) {
            switch ($operator) {
                case Op::GREATER_THAN:
                case Op::LESS_THAN:
                case Op::NOT_EQUAL:
                    return new Condition(
                        Op::LESS_THAN,
                        Version::minVersion()->copy(null, null, null, "")
                    );
                default:
                    return Condition::greaterThanMin();
            }
        } elseif ($this->isMinorWildCard) {
            $version = Version::create($this->getIntMajor(), 0, 0, $this->preRelease, $this->buildMeta);
            return new Range(
                new Condition(Op::GREATER_THAN_OR_EQUAL, $version),
                new Condition(Op::LESS_THAN, $version->getNextMajorVersion("")),
                $operator
            );
        } elseif ($this->isPatchWildCard) {
            $version = Version::create(
                $this->getIntMajor(),
                $this->getIntMinor(),
                0,
                $this->preRelease,
                $this->buildMeta
            );
            return new Range(
                new Condition(Op::GREATER_THAN_OR_EQUAL, $version),
                new Condition(Op::LESS_THAN, $version->getNextMinorVersion("")),
                $operator
            );
        } else {
            return new Condition($operator, Version::create(
                $this->getIntMajor(),
                $this->getIntMinor(),
                $this->getIntPatch(),
                $this->preRelease,
                $this->buildMeta
            ));
        }
    }
}
