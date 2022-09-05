<?php

namespace z4kn4fein\SemVer\Tests;

use PHPUnit\Framework\TestCase;
use z4kn4fein\SemVer\Version;

class CompareTest extends TestCase
{
    public function testLessThanByNumbers()
    {
        $version = Version::parse('5.2.3');
        $this->assertTrue($version->isLessThan(Version::parse('6.0.0')));
        $this->assertTrue($version->isLessThan(Version::parse('5.3.3')));
        $this->assertTrue($version->isLessThan(Version::parse('5.2.4')));
    }

    public function testLessThanByPreRelease()
    {
        $version = Version::parse('5.2.3-alpha.2');
        $this->assertTrue($version->isLessThan(Version::parse('5.2.3-alpha.2.a'))); // by pre-release part count
        $this->assertTrue($version->isLessThan(Version::parse('5.2.3-alpha.3'))); // by pre-release number comparison
        $this->assertTrue($version->isLessThan(Version::parse('5.2.3-beta'))); // by pre-release alphabetical comparison
        $this->assertTrue($version->isLessThanOrEqual(Version::parse('5.2.3-alpha.2')));
    }

    public function testPrecedenceFromSpex()
    {
        $this->assertTrue(Version::parse('1.0.0')->isLessThan(Version::parse('2.0.0')));
        $this->assertTrue(Version::parse('2.0.0')->isLessThan(Version::parse('2.1.0')));
        $this->assertTrue(Version::parse('2.1.0')->isLessThan(Version::parse('2.1.1')));

        $this->assertTrue(Version::parse('1.0.0-alpha')->isLessThan(Version::parse('1.0.0')));

        $this->assertTrue(Version::parse('1.0.0-alpha')->isLessThan(Version::parse('1.0.0-alpha.1')));
        $this->assertTrue(Version::parse('1.0.0-alpha.1')->isLessThan(Version::parse('1.0.0-alpha.beta')));
        $this->assertTrue(Version::parse('1.0.0-alpha.beta')->isLessThan(Version::parse('1.0.0-beta')));
        $this->assertTrue(Version::parse('1.0.0-beta')->isLessThan(Version::parse('1.0.0-beta.2')));
        $this->assertTrue(Version::parse('1.0.0-beta.2')->isLessThan(Version::parse('1.0.0-beta.11')));
        $this->assertTrue(Version::parse('1.0.0-beta.11')->isLessThan(Version::parse('1.0.0-rc.1')));
        $this->assertTrue(Version::parse('1.0.0-rc.1')->isLessThan(Version::parse('1.0.0')));
    }

    public function testCompareByPreReleaseNumberAlphabetical()
    {
        $version = Version::parse('5.2.3-alpha.2');
        $this->assertTrue($version->isLessThan(Version::parse('5.2.3-alpha.a')));

        $version = Version::parse('5.2.3-alpha.a');
        $this->assertTrue($version->isGreaterThan(Version::parse('5.2.3-alpha.2')));
    }

    public function testCompareByPreReleaseAndStable()
    {
        $version = Version::parse('5.2.3');
        $this->assertTrue($version->isGreaterThan(Version::parse('5.2.3-alpha')));

        $version = Version::parse('5.2.3-alpha');
        $this->assertTrue($version->isLessThan(Version::parse('5.2.3')));
    }

    public function testGreaterThanByNumbers()
    {
        $version = Version::parse('5.2.3');
        $this->assertTrue($version->isGreaterThan(Version::parse('4.0.0')));
        $this->assertTrue($version->isGreaterThan(Version::parse('5.1.3')));
        $this->assertTrue($version->isGreaterThan(Version::parse('5.2.2')));
    }

    public function testGreaterThanByPreRelease()
    {
        $version = Version::parse('5.2.3-alpha.2');
        $this->assertTrue($version->isGreaterThan(Version::parse('5.2.3-alpha'))); // by pre-release part count
        $this->assertTrue($version->isGreaterThan(Version::parse('5.2.3-alpha.1'))); // by pre-release number comparison
        $this->assertTrue($version->isGreaterThan(Version::parse('5.2.3-a'))); // by pre-release alphabetical comparison
        $this->assertTrue($version->isGreaterThanOrEqual(Version::parse('5.2.3-alpha.2')));
    }

    public function testEqual()
    {
        $version = Version::parse('5.2.3-alpha.2');
        $this->assertTrue($version->isEqual(Version::parse('5.2.3-alpha.2')));
        $this->assertFalse($version->isEqual(Version::parse('5.2.3-alpha.5')));
    }

    public function testEqualOnlyVersions()
    {
        $version = Version::parse('5.2.3');
        $this->assertTrue($version->isEqual(Version::parse('5.2.3')));
        $this->assertFalse($version->isEqual(Version::parse('5.2.4')));
    }

    public function testEqualIgnoreBuild()
    {
        $this->assertTrue(Version::equal('5.2.3-alpha.2+build.34','5.2.3-alpha.2'));
        $this->assertFalse(Version::equal('5.2.3-alpha.2+build.34','5.2.3-alpha.5'));
    }

    public function testCompareStrings()
    {
        $this->assertTrue(Version::lessThan('5.2.2','5.2.3'));
        $this->assertTrue(Version::lessThan('5.2.3-alpha.2+build.34','5.2.3-alpha.5'));
        $this->assertTrue(Version::lessThanOrEqual('5.2.3','5.2.3'));
        $this->assertTrue(Version::greaterThan('5.2.4','5.2.3'));
        $this->assertTrue(Version::greaterThan('5.2.3-alpha.6+build.34','5.2.3-alpha.5'));
        $this->assertTrue(Version::greaterThanOrEqual('5.2.3','5.2.3'));
    }
}