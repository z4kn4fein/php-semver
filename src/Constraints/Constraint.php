<?php

namespace z4kn4fein\SemVer\Constraints;

use z4kn4fein\SemVer\Patterns;
use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Traits\Iterator;
use z4kn4fein\SemVer\Traits\Singles;
use z4kn4fein\SemVer\Traits\Validator;
use z4kn4fein\SemVer\Version;

/**
 * This class describes a semantic version constraint. It provides ability to verify whether a version
 * satisfies one or more conditions within a constraint.
 *
 * @package z4kn4fein\SemVer\Constraints
 */
class Constraint
{
    use Iterator;
    use Singles;
    use Validator;

    /** @var VersionComparator[][] */
    private $comparators;

    /**
     * @param VersionComparator[][] $comparators
     */
    private function __construct(array $comparators)
    {
        $this->comparators = $comparators;
    }

    /**
     * Determines whether this constraint is satisfied by a Version or not.
     *
     * @param Version $version The version to check.
     * @return bool True when the version satisfies the constraint, otherwise false.
     */
    public function isSatisfiedBy(Version $version): bool
    {
        return self::any($this->comparators, function (array $comparator) use ($version) {
            return self::all($comparator, function (VersionComparator $condition) use ($version) {
                return $condition->isSatisfiedBy($version);
            });
        });
    }

    /**
     * @return string The string representation of the constraint.
     */
    public function __toString(): string
    {
        $result = array_map(function ($comparator) {
            return implode(' ', $comparator);
        }, $this->comparators);
        return implode(' || ', $result);
    }

    /**
     * The default constraint (>=0.0.0).
     *
     * @return Constraint The default constraint.
     */
    public static function default(): Constraint
    {
        return self::single('default-constraint', function () {
            return new Constraint([[Condition::greaterThanMin()]]);
        });
    }

    /**
     * Parses a new constraint from the given string.
     *
     * @param string $constraintString The string to parse.
     * @return Constraint|null The parsed constraint, or null if the parse fails.
     */
    public static function parseOrNull(string $constraintString): ?Constraint
    {
        try {
            return self::parse($constraintString);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Parses a new constraint from the given string.
     *
     * @param string $constraintString The string to parse.
     * @return Constraint The parsed constraint.
     * @throws SemverException When the constraint string is invalid.
     */
    public static function parse(string $constraintString): Constraint
    {
        $constraintString = trim($constraintString);
        if (empty($constraintString)) {
            return self::default();
        }

        $orParts = explode('|', $constraintString);
        $orParts = array_filter($orParts);

        $comps = array_map(function ($comparator) use ($constraintString) {
            $result = [];
            $escaped = preg_replace_callback(
                Patterns::HYPHEN_CONDITION_REGEX,
                function ($matches) use (&$result) {
                    $result[] = self::hyphenToComparator($matches);
                    return "";
                },
                $comparator
            );

            $escaped = trim($escaped);
            if (!empty($escaped) && !preg_match(Patterns::VALID_OPERATOR_CONDITION_REGEX, $escaped)) {
                throw new SemverException(sprintf("Invalid constraint: %s", $constraintString));
            }

            if (empty($escaped)) {
                return $result;
            }

            self::ensure(
                (bool)preg_match_all(
                    Patterns::OPERATOR_CONDITION_REGEX,
                    $escaped,
                    $matches,
                    PREG_SET_ORDER
                ),
                sprintf("Invalid constraint: %s", $constraintString)
            );

            foreach ($matches as $match) {
                $result[] = self::operatorToComparator($match);
            }

            return $result;
        }, $orParts);

        self::ensure(self::any($comps, function (array $comparator) {
            return !empty($comparator);
        }), sprintf("Invalid constraint: %s", $constraintString));

        return new Constraint($comps);
    }

    /**
     * @throws SemverException
     */
    private static function hyphenToComparator(array $matches): VersionComparator
    {
        $startDescriptor = new VersionDescriptor(
            $matches[1],
            isset($matches[2]) && $matches[2] !== "" ? $matches[2] : null,
            isset($matches[3]) && $matches[3] !== "" ? $matches[3] : null,
            isset($matches[4]) && $matches[4] !== "" ? $matches[4] : null,
            isset($matches[5]) && $matches[5] !== "" ? $matches[5] : null
        );
        $endDescriptor = new VersionDescriptor(
            $matches[6],
            isset($matches[7]) && $matches[7] !== "" ? $matches[7] : null,
            isset($matches[8]) && $matches[8] !== "" ? $matches[8] : null,
            isset($matches[9]) && $matches[9] !== "" ? $matches[9] : null,
            isset($matches[10]) && $matches[10] !== "" ? $matches[10] : null
        );

        return new Range(
            $startDescriptor->toComparator(Op::GREATER_THAN_OR_EQUAL),
            $endDescriptor->toComparator(Op::LESS_THAN_OR_EQUAL),
            Op::EQUAL
        );
    }

    /**
     * @throws SemverException
     */
    private static function operatorToComparator(array $matches): VersionComparator
    {
        $operator = isset($matches[1]) && $matches[1] !== "" ? $matches[1] : Op::EQUAL;
        $descriptor = new VersionDescriptor(
            $matches[2],
            isset($matches[3]) && $matches[3] !== "" ? $matches[3] : null,
            isset($matches[4]) && $matches[4] !== "" ? $matches[4] : null,
            isset($matches[5]) && $matches[5] !== "" ? $matches[5] : null,
            isset($matches[6]) && $matches[6] !== "" ? $matches[6] : null
        );
        return $descriptor->fromOperator($operator);
    }
}
