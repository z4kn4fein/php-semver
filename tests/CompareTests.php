<?php

namespace z4kn4fein\SemVer\Tests;

use PHPUnit\Framework\TestCase;
use z4kn4fein\SemVer\Version;

class CompareTests extends TestCase
{
    public function testLessThanByNumbers()
    {
        $version = Version::parse('5.2.3');
        $this->assertTrue($version->isLessThan('6.0.0'));
        $this->assertTrue($version->isLessThan('5.3.3'));
        $this->assertTrue($version->isLessThan('5.2.4'));
    }

    public function testLessThanByPreRelease()
    {
        $version = Version::parse('5.2.3-alpha.2');
        $this->assertTrue($version->isLessThan('5.2.3-alpha.2.a')); // by pre-release part count
        $this->assertTrue($version->isLessThan('5.2.3-alpha.3')); // by pre-release number comparison
        $this->assertTrue($version->isLessThan('5.2.3-beta')); // by pre-release alphabetical comparison
        $this->assertTrue($version->isLessThanOrEqual('5.2.3-alpha.2'));
    }

    public function testGreaterThanByNumbers()
    {
        $version = Version::parse('5.2.3');
        $this->assertTrue($version->isGreaterThan('4.0.0'));
        $this->assertTrue($version->isGreaterThan('5.1.3'));
        $this->assertTrue($version->isGreaterThan('5.2.2'));
    }

    public function testGreaterThanByPreRelease()
    {
        $version = Version::parse('5.2.3-alpha.2');
        $this->assertTrue($version->isGreaterThan('5.2.3-alpha')); // by pre-release part count
        $this->assertTrue($version->isGreaterThan('5.2.3-alpha.1')); // by pre-release number comparison
        $this->assertTrue($version->isGreaterThan('5.2.3-a')); // by pre-release alphabetical comparison
        $this->assertTrue($version->isGreaterThanOrEqual('5.2.3-alpha.2'));
    }

    public function testEqual()
    {
        $version = Version::parse('5.2.3-alpha.2');
        $this->assertTrue($version->isEqual('5.2.3-alpha.2'));
        $this->assertFalse($version->isEqual('5.2.3-alpha.5'));
    }

    public function testEqualIgnoreBuild()
    {
        $version = Version::parse('5.2.3-alpha.2+build.34');
        $this->assertTrue($version->isEqual('5.2.3-alpha.2'));
        $this->assertFalse($version->isEqual('5.2.3-alpha.5'));
    }
}