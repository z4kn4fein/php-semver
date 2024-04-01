<?php

declare(strict_types=1);

namespace z4kn4fein\SemVer;

use Exception;

/**
 * Version and Constraint parsing throws this exception when the parsing fails due to an invalid format.
 */
class SemverException extends Exception {}
