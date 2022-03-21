<?php

namespace z4kn4fein\SemVer\Tests;

use PHPUnit\Framework\TestCase;
use z4kn4fein\SemVer\Version;
use z4kn4fein\SemVer\VersionFormatException;

class ParseTest extends TestCase
{
    public function testEmpty()
    {
        $this->expectException(VersionFormatException::class);
        Version::parse('');
    }

    public function testInvalid()
    {
        $this->expectException(VersionFormatException::class);
        Version::parse('1.0');
    }

    public function testNegative()
    {
        $this->expectException(VersionFormatException::class);
        Version::parse('-1.0.0');
    }

    public function testValid()
    {
        $version = Version::parse('1.2.3');
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(2, $version->getMinor());
        $this->assertEquals(3, $version->getPatch());
    }

    public function testWithPreReleaseAndBuild()
    {
        $version = Version::parse('1.2.3-alpha.1+build.3.b');
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(2, $version->getMinor());
        $this->assertEquals(3, $version->getPatch());
        $this->assertEquals('alpha.1', (string)$version->getPreRelease());
        $this->assertEquals('build.3.b', $version->getBuildMeta());
        $this->assertEquals('1.2.3-alpha.1+build.3.b', (string)$version);
    }

    public function testWithPreRelease()
    {
        $version = Version::parse('1.2.3-alpha.1.a.34');
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(2, $version->getMinor());
        $this->assertEquals(3, $version->getPatch());
        $this->assertEquals('alpha.1.a.34', (string)$version->getPreRelease());
        $this->assertEmpty($version->getBuildMeta());
    }

    public function testWithBuild()
    {
        $version = Version::parse('1.2.3+build.3.b');
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(2, $version->getMinor());
        $this->assertEquals(3, $version->getPatch());
        $this->assertEquals('build.3.b', $version->getBuildMeta());
        $this->assertEmpty($version->getPreRelease());
    }
}