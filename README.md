# php-semver
[![Build Status](https://github.com/z4kn4fein/php-semver/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/z4kn4fein/php-semver/actions/workflows/ci.yml)
[![Coverage Status](https://img.shields.io/codecov/c/github/z4kn4fein/php-semver.svg)](https://codecov.io/gh/z4kn4fein/php-semver)
[![Latest Stable Version](https://poser.pugx.org/z4kn4fein/php-semver/version)](https://packagist.org/packages/z4kn4fein/php-semver)
[![Total Downloads](https://poser.pugx.org/z4kn4fein/php-semver/downloads)](https://packagist.org/packages/z4kn4fein/php-semver)
[![Latest Unstable Version](https://poser.pugx.org/z4kn4fein/php-semver/v/unstable)](https://packagist.org/packages/z4kn4fein/php-semver)

Semantic Versioning library for PHP. It implements the full [semantic version 2.0.0](https://semver.org/spec/v2.0.0.html) specification and
provides ability to **parse**, **compare**, and **increment** semantic versions along with validation against **constraints**.

## Requirements
[PHP](https://www.php.net/) >= 7.1

## Install with [Composer](https://getcomposer.org/)
```shell
composer require z4kn4fein/php-semver
```

## Usage
The following options are supported to construct a `Version`:
1. Building part by part with `Version::create()`.

   ```php
   Version::create(3, 5, 2, "alpha", "build");
   ```

2. Parsing from a string with `Version::parse()` or `Version::parseOrNull()`.

   ```php
   Version::parse("3.5.2-alpha+build");
   ```

The following information is accessible on a constructed `Version` object:
```php
<?php

use z4kn4fein\SemVer\Version;

$version = Version::parse("2.5.6-alpha.12+build.34");

echo $version->getMajor();         // 2
echo $version->getMinor();         // 5
echo $version->getPatch();         // 6
echo $version->getPreRelease();    // alpha.12
echo $version->getBuildMeta();     // build.34
echo $version->isPreRelease();     // true
echo $version->isStable();         // false
echo $version->withoutSuffixes();  // 2.5.6
echo $version;                     // 2.5.6-alpha.12+build.34
```

### Strict vs. Loose Parsing
By default, the version parser considers partial versions like `1.0` and versions starting with the `v` prefix invalid.
This behaviour can be turned off by setting the `strict` parameter to `false`.
```php
echo Version::parse("v2.3-alpha");             // exception
echo Version::parse("2.1");                    // exception
echo Version::parse("v3");                     // exception

echo Version::parse("v2.3-alpha", false);      // 2.3.0-alpha
echo Version::parse("2.1", false);             // 2.1.0
echo Version::parse("v3", false);              // 3.0.0
```

## Compare
It is possible to compare two `Version` objects with the following comparison methods.
```php
<?php

use z4kn4fein\SemVer\Version;

// with static methods
echo Version::lessThan("2.3.4", "2.4.1");                            // true
echo Version::lessThanOrEqual("2.4.1", "2.4.1");                     // true
echo Version::greaterThan("2.3.1-alpha.5", "2.3.1-alpha.3");         // true
echo Version::greaterThanOrEqual("3.2.3","3.2.2");                   // true
echo Version::equal("3.2.3","3.2.3+build.3");                        // true
echo Version::notEqual("3.2.3","2.2.4");                             // true

// compare() or compareString()
echo Version::compare(Version::parse("2.3.4"), Version::parse("2.4.1"));  // -1
echo Version::compare(Version::parse("2.3.4"), Version::parse("2.3.4"));  // 0
echo Version::compare(Version::parse("2.3.4"), Version::parse("2.2.0"));  // 1

echo Version::compareString("2.3.4", "2.4.1");                            // -1
echo Version::compareString("2.3.4", "2.3.4");                            // 0
echo Version::compareString("2.3.4", "2.2.0");                            // 1


// with instance methods
$version = Version::parse("2.5.6-alpha.12+build.34");

echo $version->isLessThan(Version::parse("2.3.1"));                  // false
echo $version->isLessThanOrEqual(Version::parse("2.5.6-alpha.15"));  // true
echo $version->isGreaterThan(Version::parse("2.5.6"));               // false
echo $version->isLessThanOrEqual(Version::parse("2.5.6-alpha.12"));  // true
echo $version->isEqual(Version::parse("2.5.6-alpha.12+build.56"));   // true
echo $version->isNotEqual(Version::parse("2.2.4"));                  // true
```

### Sort

`Version::sort()` and `Version::sortString()` are available to sort an array of versions.
```php
<?php

use z4kn4fein\SemVer\Version;

$versions = array_map(function(string $version) {
    return Version::parse($version);
}, [
    "1.0.1",
    "1.0.1-alpha",
    "1.0.1-alpha.beta",
    "1.0.1-alpha.3",
    "1.0.1-alpha.2",
    "1.1.0",
    "1.1.0+build",
]);

$sorted = Version::sort($versions);

// The result:
//   "1.0.1-alpha"
//   "1.0.1-alpha.2"
//   "1.0.1-alpha.3"
//   "1.0.1-alpha.beta"
//   "1.0.1"
//   "1.1.0"
//   "1.1.0+build"
```

You might want to sort in reverse order, then you can use `Version::rsort()` or `Version::rsortString()`.
```php
<?php

use z4kn4fein\SemVer\Version;

$versions = array_map(function(string $version) {
    return Version::parse($version);
}, [
    "1.0.1",
    "1.0.1-alpha",
    "1.0.1-alpha.beta",
    "1.0.1-alpha.3",
    "1.0.1-alpha.2",
    "1.1.0",
    "1.1.0+build",
]);

$sorted = Version::rsort($versions);

// The result:
//   "1.1.0"
//   "1.1.0+build"
//   "1.0.1"
//   "1.0.1-alpha.beta"
//   "1.0.1-alpha.3"
//   "1.0.1-alpha.2"
//   "1.0.1-alpha"
```

`Version::compare()` and `Version::compareString()` methods also can be used as callback for `usort()` to sort an array of versions.
```php
<?php

use z4kn4fein\SemVer\Version;

$versions = array_map(function(string $version) {
    return Version::parse($version);
}, [
    "1.0.1",
    "1.0.1-alpha",
    "1.0.1-alpha.beta",
    "1.0.1-alpha.3",
    "1.0.1-alpha.2",
    "1.1.0",
    "1.1.0+build",
]);

usort($versions, ["z4kn4fein\SemVer\Version", "compare"]);

// The result:
//   "1.0.1-alpha"
//   "1.0.1-alpha.2"
//   "1.0.1-alpha.3"
//   "1.0.1-alpha.beta"
//   "1.0.1"
//   "1.1.0"
//   "1.1.0+build"
```

## Constraints
With constraints, it's possible to validate whether a version satisfies a set of rules or not.
A constraint can be described as one or more conditions combined with logical `OR` and `AND` operators.

### Conditions
Conditions are usually composed of a comparison operator and a version like `>=1.2.0`.
The condition `>=1.2.0` would be met by any version that greater than or equal to `1.2.0`.

Supported comparison operators:
- `=` Equal (equivalent to no operator: `1.2.0` means `=1.2.0`)
- `!=` Not equal
- `<` Less than
- `<=` Less than or equal
- `>` Greater than
- `>=` Greater than or equal

Conditions can be joined together with whitespace, representing the `AND` logical operator between them.
The `OR` operator can be expressed with `||` or `|` between condition sets.

For example, the constraint `>=1.2.0 <3.0.0 || >4.0.0` translates to: *Only those versions are allowed that are either greater than or
equal to `1.2.0` {**AND**} less than `3.0.0` {**OR**} greater than `4.0.0`*.

We can notice that the first part of the previous constraint (`>=1.2.0 <3.0.0`) is a simple semantic version range.
There are more ways to express version ranges; the following section will go through all the available options.

### Range Conditions
There are particular range indicators which are sugars for more extended range expressions.

- **X-Range**: The `x`, `X`, and `*` characters can be used as a wildcard for the numeric parts of a version.
   - `1.2.x` translates to `>=1.2.0 <1.3.0-0`
   - `1.x` translates to `>=1.0.0 <2.0.0-0`
   - `*` translates to `>=0.0.0`

  In partial version expressions, the missing numbers are treated as wildcards.
   - `1.2` means `1.2.x` which finally translates to `>=1.2.0 <1.3.0-0`
   - `1` means `1.x` or `1.x.x` which finally translates to `>=1.0.0 <2.0.0-0`

- **Hyphen Range**: Describes an inclusive version range. Wildcards are evaluated and taken into account in the final range.
   - `1.0.0 - 1.2.0` translates to `>=1.0.0 <=1.2.0`
   - `1.1 - 1.4.0` means `>=(>=1.1.0 <1.2.0-0) <=1.4.0` which finally translates to `>=1.1.0 <=1.4.0`
   - `1.1.0 - 2` means `>=1.1.0 <=(>=2.0.0 <3.0.0-0)` which finally translates to `>=1.1.0 <3.0.0-0`

- **Tilde Range (`~`)**: Describes a patch level range when the minor version is specified or a minor level range when it's not.
   - `~1.0.1` translates to `>=1.0.1 <1.1.0-0`
   - `~1.0` translates to `>=1.0.0 <1.1.0-0`
   - `~1` translates to `>=1.0.0 <2.0.0-0`
   - `~1.0.0-alpha.1` translates to `>=1.0.1-alpha.1 <1.1.0-0`

- **Caret Range (`^`)**: Describes a range with regard to the most left non-zero part of the version.
   - `^1.1.2` translates to `>=1.1.2 <2.0.0-0`
   - `^0.1.2` translates to `>=0.1.2 <0.2.0-0`
   - `^0.0.2` translates to `>=0.0.2 <0.0.3-0`
   - `^1.2` translates to `>=1.2.0 <2.0.0-0`
   - `^1` translates to `>=1.0.0 <2.0.0-0`
   - `^0.1.2-alpha.1` translates to `>=0.1.2-alpha.1 <0.2.0-0`

### Validation
Let's see how we can determine whether a version satisfies a constraint or not.
```php
<?php

use z4kn4fein\SemVer\Version;
use z4kn4fein\SemVer\Constraints\Constraint;

$constraint = Constraint::parse(">=1.2.0");
$version = Version::parse("1.2.1");

echo $version->isSatisfying($constraint);     // true
echo $constraint->isSatisfiedBy($version);    // true

// Or using the static satisfies() method with strings:
echo Version::satisfies("1.2.1", ">=1.2.0");  // true
```

## Increment
`Version` objects can produce incremented versions of themselves with the `getNext{Major|Minor|Patch|PreRelease}Version` methods.
These methods can be used to determine the next version in order incremented by the according part.
`Version` objects are **immutable**, so each incrementing function creates a new `Version`.

This example shows how the incrementation works on a stable version:
```php
<?php

use z4kn4fein\SemVer\Version;
use z4kn4fein\SemVer\Inc;

$stableVersion = Version::create(1, 0, 0);

echo $stableVersion->getNextMajorVersion();        // 2.0.0
echo $stableVersion->getNextMinorVersion();        // 1.1.0
echo $stableVersion->getNextPatchVersion();        // 1.0.1
echo $stableVersion->getNextPreReleaseVersion();   // 1.0.1-0

// or with the inc() method:
echo $stableVersion->inc(Inc::MAJOR);              // 2.0.0
echo $stableVersion->inc(Inc::MINOR);              // 1.1.0
echo $stableVersion->inc(Inc::PATCH);              // 1.0.1
echo $stableVersion->inc(Inc::PRE_RELEASE);        // 1.0.1-0
```

In case of an unstable version:
```php
<?php

use z4kn4fein\SemVer\Version;
use z4kn4fein\SemVer\Inc;

$unstableVersion = Version::parce("1.0.0-alpha.2+build.1");

echo $unstableVersion->getNextMajorVersion();        // 2.0.0
echo $unstableVersion->getNextMinorVersion();        // 1.1.0
echo $unstableVersion->getNextPatchVersion();        // 1.0.0
echo $unstableVersion->getNextPreReleaseVersion();   // 1.0.0-alpha.3

// or with the inc() method:
echo $unstableVersion->inc(Inc::MAJOR);              // 2.0.0
echo $unstableVersion->inc(Inc::MINOR);              // 1.1.0
echo $unstableVersion->inc(Inc::PATCH);              // 1.0.0
echo $unstableVersion->inc(Inc::PRE_RELEASE);        // 1.0.0-alpha.3
```

Each incrementing function provides the option to set a pre-release identity on the incremented version.
```php
<?php

use z4kn4fein\SemVer\Version;
use z4kn4fein\SemVer\Inc;

$version = Version::parce("1.0.0-alpha.1");

echo $version->getNextMajorVersion("beta");         // 2.0.0-beta
echo $version->getNextMinorVersion("");             // 1.1.0-0
echo $version->getNextPatchVersion("alpha");        // 1.0.1-alpha
echo $version->getNextPreReleaseVersion("alpha");   // 1.0.0-alpha.2

// or with the inc() method:
echo $version->inc(Inc::MAJOR, "beta");             // 2.0.0-beta
echo $version->inc(Inc::MINOR, "");                 // 1.1.0-0
echo $version->inc(Inc::PATCH, "alpha");            // 1.0.1-alpha
echo $version->inc(Inc::PRE_RELEASE, "alpha");      // 1.0.0-alpha.2
```

## Copy
It's possible to make a copy of a particular version with the `copy()` method.
It allows altering the copied version's properties with optional parameters.
```php
$version = Version::parse("1.0.0-alpha.2+build.1");

echo $version->copy();                                        // 1.0.0-alpha.2+build.1
echo $version->copy(3);                                       // 3.0.0-alpha.2+build.1
echo $version->copy(null, 4);                                 // 1.4.0-alpha.2+build.1
echo $version->copy(null, null, 5);                           // 1.0.5-alpha.2+build.1
echo $version->copy(null, null, null, "alpha.4");             // 1.0.0-alpha.4+build.1
echo $version->copy(null, null, null, null, "build.3");       // 1.0.0-alpha.2+build.3
echo $version->copy(3, 4, 5);                                 // 3.4.5-alpha.2+build.1
```
> **Note**:
> Without setting any optional parameter, the `copy()` method will produce an exact copy of the original version.

## Invalid version handling
When the version or constraint parsing fails due to an invalid format, the library throws a specific `SemverException`.
> **Note**:
> The `Version::parseOrNull()` and `Constraint::parseOrNull()` methods can be used for exception-less conversions as they return `null` when the parsing fails.

## Contact & Support
- Create an [issue](https://github.com/z4kn4fein/php-semver/issues) for bug reports and feature requests.
- Start a [discussion](https://github.com/z4kn4fein/php-semver/discussions) for your questions and ideas.
- Add a ⭐️ to support the project!
