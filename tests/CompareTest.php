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

    public function testPrecedenceFromSpec()
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

        $this->assertFalse(Version::notEqual('5.2.3-alpha.2+build.34','5.2.3-alpha.2'));
        $this->assertTrue(Version::notEqual('5.2.3-alpha.2+build.34','5.2.3-alpha.5'));
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

    public function testCompareWithCompare()
    {
        $this->assertEquals(-1, Version::compare(Version::parse('5.2.2'), Version::parse('5.2.3')));
        $this->assertEquals(-1, Version::compare(Version::parse('5.2.3-alpha.2+build.34'),Version::parse('5.2.3-alpha.5')));
        $this->assertEquals(0, Version::compare(Version::parse('5.2.3'),Version::parse('5.2.3')));
        $this->assertEquals(1, Version::compare(Version::parse('5.2.4'),Version::parse('5.2.3')));
        $this->assertEquals(1, Version::compare(Version::parse('5.2.3-alpha.6+build.34'),Version::parse('5.2.3-alpha.5')));
        $this->assertEquals(0, Version::compare(Version::parse('5.2.3'),Version::parse('5.2.3')));
    }

    public function testCompareStringsWithCompare()
    {
        $this->assertEquals(-1, Version::compareString('5.2.2','5.2.3'));
        $this->assertEquals(-1, Version::compareString('5.2.3-alpha.2+build.34','5.2.3-alpha.5'));
        $this->assertEquals(0, Version::compareString('5.2.3','5.2.3'));
        $this->assertEquals(1, Version::compareString('5.2.4','5.2.3'));
        $this->assertEquals(1, Version::compareString('5.2.3-alpha.6+build.34','5.2.3-alpha.5'));
        $this->assertEquals(0, Version::compareString('5.2.3','5.2.3'));
    }

    public function testUsort()
    {
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

        $this->assertEquals("1.0.1-alpha", (string)$versions[0]);
        $this->assertEquals("1.0.1-alpha.2", (string)$versions[1]);
        $this->assertEquals("1.0.1-alpha.3", (string)$versions[2]);
        $this->assertEquals("1.0.1-alpha.beta", (string)$versions[3]);
        $this->assertEquals("1.0.1", (string)$versions[4]);
        $this->assertEquals("1.1.0", (string)$versions[5]);
        $this->assertEquals("1.1.0+build", (string)$versions[6]);
    }

    public function testSort()
    {
        $arr = [
            "1.0.1",
            "1.0.1-alpha",
            "1.0.1-alpha.beta",
            "1.0.1-alpha.3",
            "1.0.1-alpha.2",
            "1.1.0",
            "1.1.0+build",
        ];

        $versions = array_map(function(string $version) {
            return Version::parse($version);
        }, $arr);

        $sorted = Version::sort($versions);

        $this->assertEquals("1.0.1-alpha", (string)$sorted[0]);
        $this->assertEquals("1.0.1-alpha.2", (string)$sorted[1]);
        $this->assertEquals("1.0.1-alpha.3", (string)$sorted[2]);
        $this->assertEquals("1.0.1-alpha.beta", (string)$sorted[3]);
        $this->assertEquals("1.0.1", (string)$sorted[4]);
        $this->assertEquals("1.1.0", (string)$sorted[5]);
        $this->assertEquals("1.1.0+build", (string)$sorted[6]);
        
        $sortedString = Version::sortString($arr);

        $this->assertEquals("1.0.1-alpha", $sortedString[0]);
        $this->assertEquals("1.0.1-alpha.2", $sortedString[1]);
        $this->assertEquals("1.0.1-alpha.3", $sortedString[2]);
        $this->assertEquals("1.0.1-alpha.beta", $sortedString[3]);
        $this->assertEquals("1.0.1", $sortedString[4]);
        $this->assertEquals("1.1.0", $sortedString[5]);
        $this->assertEquals("1.1.0+build", $sortedString[6]);
    }

    public function testRsort()
    {
        $arr = [
            "1.0.1",
            "1.0.1-alpha",
            "1.0.1-alpha.beta",
            "1.0.1-alpha.3",
            "1.0.1-alpha.2",
            "1.1.0",
        ];

        $versions = array_map(function(string $version) {
            return Version::parse($version);
        }, $arr);

        $sorted = Version::rsort($versions);

        $this->assertEquals("1.0.1-alpha", (string)$sorted[5]);
        $this->assertEquals("1.0.1-alpha.2", (string)$sorted[4]);
        $this->assertEquals("1.0.1-alpha.3", (string)$sorted[3]);
        $this->assertEquals("1.0.1-alpha.beta", (string)$sorted[2]);
        $this->assertEquals("1.0.1", (string)$sorted[1]);
        $this->assertEquals("1.1.0", (string)$sorted[0]);

        $sortedString = Version::rsortString($arr);

        $this->assertEquals("1.0.1-alpha", $sortedString[5]);
        $this->assertEquals("1.0.1-alpha.2", $sortedString[4]);
        $this->assertEquals("1.0.1-alpha.3", $sortedString[3]);
        $this->assertEquals("1.0.1-alpha.beta", $sortedString[2]);
        $this->assertEquals("1.0.1", $sortedString[1]);
        $this->assertEquals("1.1.0", $sortedString[0]);
    }
}