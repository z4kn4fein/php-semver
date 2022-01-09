# php-semver
[![Build Status](https://travis-ci.com/z4kn4fein/php-semver.svg?branch=master)](https://travis-ci.com/z4kn4fein/php-semver)
[![Coverage Status](https://img.shields.io/codecov/c/github/z4kn4fein/php-semver.svg)](https://codecov.io/gh/z4kn4fein/php-semver)
[![Latest Stable Version](https://poser.pugx.org/z4kn4fein/php-semver/version)](https://packagist.org/packages/z4kn4fein/php-semver)
[![Total Downloads](https://poser.pugx.org/z4kn4fein/php-semver/downloads)](https://packagist.org/packages/z4kn4fein/php-semver)
[![Latest Unstable Version](https://poser.pugx.org/z4kn4fein/php-semver/v/unstable)](https://packagist.org/packages/z4kn4fein/php-semver)

Semantic version utility library with parser and comparator written in PHP. It provides full support for the [semver 2.0.0](https://semver.org/spec/v2.0.0.html) standards. 

## Requirements
[PHP](https://www.php.net/) >= 5.5

## Install with [Composer](https://getcomposer.org/)
```shell
composer require z4kn4fein/php-semver
```

## Usage
The following options are supported to construct a `Version`:
1. Building part by part with `Version.create()`.

   ```php
   Version::create(3, 5, 2, "alpha", "build")
   ```

2. Parsing from a string with `Version.parse()`.

   ```php
   Version::parse("3.5.2-alpha+build")
   ```

The following information is accessible on a constructed `Version` object:
```php
<?php

use z4kn4fein\SemVer\Version;

$version = Version::parse('2.5.6-alpha.12+build.34');

echo $version->getMajor();                  // 2
echo $version->getMinor();                  // 5
echo $version->getPatch();                  // 6
echo $version->getPreRelease();             // alpha.12
echo $version->getBuildMeta();              // build.34
echo (string)$version;                      // 2.5.6-alpha.12+build.34
```
### Comparing two versions
It is possible to compare two `Version` objects with the following comparison methods.
```php
<?php

use z4kn4fein\SemVer\Version;

// with static methods
echo Version::lessThan('2.3.4', '2.4.1');                    // true
echo Version::lessThanOrEqual('2.4.1', '2.4.1');             // true
echo Version::greaterThan('2.3.1-alpha.5', '2.3.1-alpha.3'); // true
echo Version::greaterThanOrEqual('3.2.3','3.2.2');           // true
echo Version::equal('3.2.3','3.2.3+build.3');                // true

// with instance methods
$version = Version::parse('2.5.6-alpha.12+build.34');

echo $version->isLessThan('2.3.1');                  // false
echo $version->isLessThanOrEqual('2.5.6-alpha.15');  // true
echo $version->isGreaterThan('2.5.6');               // false
echo $version->isLessThanOrEqual('2.5.6-alpha.12');  // true
echo $version->isEqual('2.5.6-alpha.12+build.56');   // true
```
### Increment
`Version` objects can produce incremented versions of themselves with the `getNext{Major|Minor|Patch|PreRelease}Version` methods.
These methods can be used to determine the next version in order incremented by the according part.
```php
<?php

use z4kn4fein\SemVer\Version;

$version = Version::create(2, 3, 5, "alpha.4", "build.2");

echo (string)$version->getNextMajorVersion();        // 3.0.0
echo (string)$version->getNextMinorVersion();        // 2.4.0
echo (string)$version->getNextPatchVersion();        // 2.3.5
echo (string)$version->getNextPreReleaseVersion();   // 2.3.5-alpha.5

$version = Version::create(1, 0, 0);

echo (string)$version->getNextMajorVersion();        // 2.0.0
echo (string)$version->getNextMinorVersion();        // 1.1.0
echo (string)$version->getNextPatchVersion();        // 1.0.1
echo (string)$version->getNextPreReleaseVersion();   // 1.0.1-0
```

### Copy
It's possible to make a copy of a particular version with the `copy()` method.
It allows altering the copied version's properties with optional parameters.
```php
$version = Version::parse("1.0.0-alpha.2+build.1");

echo (string)$version->copy(3)                                       // 3.0.0
echo (string)$version->copy(null, 4)                                 // 1.4.0
echo (string)$version->copy(null, null, 5)                           // 1.0.5
echo (string)$version->copy(null, null, null, "alpha.4")             // 1.0.0-alpha.4
echo (string)$version->copy(null, null, null, null, "build.3")       // 1.0.0-alpha.2+build.3
echo (string)$version->copy(3, 4, 5)                                 // 3.4.5-alpha.2+build.1
```
> Without setting any optional parameter, the `copy()` method will produce an exact copy of the original version.

## Invalid version handling
When the version parsing fails due to an invalid format, the library throws a specific `VersionFormatException`.
