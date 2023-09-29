<?php

namespace z4kn4fein\SemVer;

use z4kn4fein\SemVer\Traits\PrimitiveComparable;
use z4kn4fein\SemVer\Traits\Singles;
use z4kn4fein\SemVer\Traits\Validator;

/**
 * @internal
 */
class PreRelease
{
    use PrimitiveComparable;
    use Validator;
    use Singles;

    /** @var mixed[] */
    private $preReleaseParts;

    /**
     * PreRelease constructor.
     *
     * @param mixed[] $preReleaseParts the pre-release parts
     */
    private function __construct(array $preReleaseParts)
    {
        $this->preReleaseParts = $preReleaseParts;
    }

    /**
     * @return string the string representation of the pre-release
     */
    public function __toString()
    {
        return implode('.', $this->preReleaseParts);
    }

    /**
     * @return string the identity of the pre-release tag
     */
    public function identity(): string
    {
        return $this->preReleaseParts[0];
    }

    /**
     * @return PreRelease the incremented pre-release
     */
    public function increment(): PreRelease
    {
        $result = $this->copy();
        $lastNumericIndex = -1;
        foreach ($result->preReleaseParts as $key => $part) {
            if (is_numeric($part)) {
                $lastNumericIndex = $key;
            }
        }

        if (-1 != $lastNumericIndex) {
            $result->preReleaseParts[$lastNumericIndex] = intval($result->preReleaseParts[$lastNumericIndex]) + 1;
        } else {
            $result->preReleaseParts[] = 0;
        }

        return $result;
    }

    /**
     * The default pre-release tag (-0).
     *
     * @return PreRelease the default pre-release tag
     */
    public static function default(): PreRelease
    {
        return self::single('default-pre-release', function () {
            return new PreRelease([0]);
        });
    }

    /**
     * @param string $preReleaseString the pre-release string
     *
     * @throws SemverException when the given pre-release string is invalid
     *
     * @return PreRelease the parsed pre-release part
     */
    public static function parse(string $preReleaseString): PreRelease
    {
        $preReleaseString = trim($preReleaseString);
        if ('' === $preReleaseString) {
            return self::default();
        }

        $preRelease = new PreRelease(explode('.', $preReleaseString));
        $preRelease->validate();

        return $preRelease;
    }

    /**
     * @param PreRelease $p1 the left side of the comparison
     * @param PreRelease $p2 the right side of the comparison
     *
     * @return int -1 when $p1 < $p2, 0 when $p1 == $p2, 1 when $p1 > $p2
     */
    public static function compare(PreRelease $p1, PreRelease $p2): int
    {
        $v1Size = count($p1->preReleaseParts);
        $v2Size = count($p2->preReleaseParts);

        $count = min($v1Size, $v2Size);

        for ($i = 0; $i < $count; ++$i) {
            $part = self::comparePart($p1->preReleaseParts[$i], $p2->preReleaseParts[$i]);
            if (0 != $part) {
                return $part;
            }
        }

        return self::comparePrimitive($v1Size, $v2Size);
    }

    /**
     * @return PreRelease the copied pre-release
     */
    private function copy(): PreRelease
    {
        return new PreRelease($this->preReleaseParts);
    }

    /**
     * @throws SemverException when the any part of the tag is invalid
     */
    private function validate(): void
    {
        foreach ($this->preReleaseParts as $part) {
            if (preg_match(Patterns::ONLY_NUMBER_REGEX, $part) && strlen($part) > 1 && '0' === $part[0]) {
                throw new SemverException(sprintf(
                    "The pre-release part '%s' is numeric but contains a leading zero.",
                    $part
                ));
            }

            self::ensure((bool) preg_match(Patterns::ONLY_ALPHANUMERIC_OR_HYPHEN_REGEX, $part), sprintf(
                "The pre-release part '%s' contains invalid character.",
                $part
            ));
        }
    }

    /**
     * @param int|string $a the left side of the comparison
     * @param int|string $b the right side of the comparison
     *
     * @return int -1 when $a < $b, 0 when $a == $b, 1 when $v1 > $b
     */
    private static function comparePart($a, $b): int
    {
        if (is_numeric($a) && !is_numeric($b)) {
            return -1;
        }

        if (!is_numeric($a) && is_numeric($b)) {
            return 1;
        }

        return is_numeric($a) && is_numeric($b)
            ? self::comparePrimitive(intval($a), intval($b))
            : self::comparePrimitive($a, $b);
    }
}
