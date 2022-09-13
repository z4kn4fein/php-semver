<?php

namespace z4kn4fein\SemVer;

/**
 * @internal
 */
class Patterns
{
    // Numeric identifier pattern. (used for parsing major, minor, and patch)
    const NUMERIC = "0|[1-9]\\d*";

    // Alphanumeric or hyphen pattern.
    const ALPHANUMERIC_OR_HYPHEN = "[0-9a-zA-Z-]";

    // Letter or hyphen pattern.
    const LETTER_OR_HYPHEN = "[a-zA-Z-]";

    // Non-numeric identifier pattern. (used for parsing pre-release)
    const NON_NUMERIC = "\\d*" . self::LETTER_OR_HYPHEN . self::ALPHANUMERIC_OR_HYPHEN . "*";

    // Dot-separated numeric identifier pattern. (<major>.<minor>.<patch>)
    const CORE_VERSION = "(" . self::NUMERIC . ")\\.(" . self::NUMERIC . ")\\.(". self::NUMERIC . ")";

    // Dot-separated loose numeric identifier pattern. (<major>(.<minor>)?(.<patch>)?)
    const LOOSE_CORE_VERSION = "(" . self::NUMERIC . ")(?:\\.(" . self::NUMERIC . "))?(?:\\.(" . self::NUMERIC . "))?";

    // Numeric or non-numeric pre-release part pattern.
    const PRE_RELEASE_PART = "(?:" . self::NUMERIC . "|" . self::NON_NUMERIC . ")";

    // Pre-release identifier pattern. A hyphen followed by dot-separated
    // numeric or non-numeric pre-release parts.
    const PRE_RELEASE = "(?:-(" . self::PRE_RELEASE_PART . "(?:\\." . self::PRE_RELEASE_PART . ")*))";

    // Build-metadata identifier pattern. A + sign followed by dot-separated
    // alphanumeric build-metadata parts.
    const BUILD = "(?:\\+(" . self::ALPHANUMERIC_OR_HYPHEN . "+(?:\\." . self::ALPHANUMERIC_OR_HYPHEN . "+)*))";

    // List of allowed operations in a condition.
    const ALLOWED_OPERATORS = "||=|!=|<|<=|=<|>|>=|=>|\\^|~>|~";

    // Numeric identifier pattern for parsing conditions.
    const X_RANGE_NUMERIC = self::NUMERIC . "|x|X|\\*";

    // X-RANGE version: 1.x | 1.2.* | 1.1.X
    // phpcs:ignore
    const X_RANGE_VERSION = "(" . self::X_RANGE_NUMERIC . ")(?:\\.(" . self::X_RANGE_NUMERIC . ")(?:\\.(" . self::X_RANGE_NUMERIC . ")(?:" . self::PRE_RELEASE . ")?" . self::BUILD . "?)?)?";

    // Pattern that only matches numbers.
    const ONLY_NUMBER_REGEX = "/^[0-9]+$/";

    // Pattern that only matches alphanumeric or hyphen characters.
    const ONLY_ALPHANUMERIC_OR_HYPHEN_REGEX = "/^" . self::ALPHANUMERIC_OR_HYPHEN . "+$/";

    // Version parsing pattern: 1.2.3-alpha+build
    const VERSION_REGEX = "/^" . self::CORE_VERSION . self::PRE_RELEASE . "?" . self::BUILD . "?\$/";

    // Prefixed version parsing pattern: v1.2-alpha+build
    const LOOSE_VERSION_REGEX = "/^v?" . self::LOOSE_CORE_VERSION . self::PRE_RELEASE . "?" . self::BUILD . "?\$/";

    // Operator condition: >=1.2.*
    const OPERATOR_CONDITION = "(" . self::ALLOWED_OPERATORS . ")\\s*v?(?:" . self::X_RANGE_VERSION . ")";

    // Operator condition: >=1.2.*
    const OPERATOR_CONDITION_REGEX = "/" . self::OPERATOR_CONDITION . "/";

    // Operator condition: >=1.2.*
    const VALID_OPERATOR_CONDITION_REGEX = "/^(\\s*" . self::OPERATOR_CONDITION . "\\s*?)+\$/";

    // Hyphen range condition: 1.2.* - 2.0.0
    // phpcs:ignore
    const HYPHEN_CONDITION_REGEX = "/\\s*v?(?:" . self::X_RANGE_VERSION . ")\\s+-\\s+v?(?:" . self::X_RANGE_VERSION . ")\\s*/";

    // Wildcard characters.
    const WILDCARDS = array("*", "x", "X");

    // Operators.
    const COMPARISON_OPERATORS = array("=", "!=", ">", ">=", "=>", "<", "<=", "=<");
    const TILDE_OPERATORS = array("~>", "~");
    const CARET_OPERATOR = "^";

    /**
     * Determines whether a string is a wildcard or not.
     *
     * @param string $text The string to check.
     * @return bool True when the string is wildcard, otherwise false.
     */
    public static function isWildcard(string $text): bool
    {
        return in_array($text, self::WILDCARDS, true);
    }
}
