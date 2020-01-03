# php-semver
[![Build Status](https://travis-ci.com/z4kn4fein/php-semver.svg?branch=master)](https://travis-ci.com/z4kn4fein/php-semver)
[![Coverage Status](https://img.shields.io/codecov/c/github/z4kn4fein/php-semver.svg)](https://codecov.io/gh/z4kn4fein/php-semver)
[![Latest Stable Version](https://poser.pugx.org/z4kn4fein/php-semver/version)](https://packagist.org/packages/z4kn4fein/php-semver)
[![Total Downloads](https://poser.pugx.org/z4kn4fein/php-semver/downloads)](https://packagist.org/packages/z4kn4fein/php-semver)
[![Latest Unstable Version](https://poser.pugx.org/z4kn4fein/php-semver/v/unstable)](https://packagist.org/packages/z4kn4fein/php-semver)

Semantic version utility library with parser and comparator written in PHP. It provides full support for the [semver 2.0.0](https://semver.org/spec/v2.0.0.html) standards. 

## Requirements
[PHP](https://www.php.net/) >= 5.5

## Installation with [Composer](https://getcomposer.org/)
```shell
composer require z4kn4fein/php-semver
```

## Usage
#### Parsing and available properties
```php
<?php

use z4kn4fein\SemVer\Version;

$version = Version::parse('2.5.6-alpha.12+build.34');

echo $version->getMajor();                  // 2
echo $version->getMinor();                  // 5
echo $version->getPatch();                  // 6
echo (string)$version->getPreRelease();     // alpha.12
echo $version->getBuildMeta();              // build.34
echo (string)$version;                      // 2.5.6-alpha.12+build.34
```
#### Comparing two versions
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
#### Producing incremented versions
```php
<?php

use z4kn4fein\SemVer\Version;

$version = Version::create(2, 3, 5, "alpha.4", "build.2");

echo (string)$version->getNextMajorVersion();        // 3.0.0
echo (string)$version->getNextMinorVersion();        // 2.4.0
echo (string)$version->getNextPatchVersion();        // 2.3.5
echo (string)$version->getNextPreReleaseVersion();   // 2.3.5-alpha.5
```
## Invalid version handling
When the version parsing fails due to an invalid format, the library throws a specific `VersionFormatException`.
