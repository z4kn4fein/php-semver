<?php

namespace z4kn4fein\SemVer\Tests;

use PHPUnit\Framework\TestCase;
use z4kn4fein\SemVer\Version;
use z4kn4fein\SemVer\SemverException;

class CreateTest extends TestCase
{
    public function testInvalidMajor()
    {
        $this->expectException(SemverException::class);
        Version::create(-1, 0,0);
    }

    public function testInvalidMinor()
    {
        $this->expectException(SemverException::class);
        Version::create(0, -1,0);
    }

    public function testInvalidPatch()
    {
        $this->expectException(SemverException::class);
        Version::create(0, 0,-1);
    }

    public function testAllZeros()
    {
        $this->assertNotNull(Version::create(0, 0,0));
    }

    public function testCopy()
    {
        $version = Version::create(0, 0,0, "alpha", "build");
        $this->assertEquals($version, $version->copy());

        $this->assertEquals("2.0.0-alpha+build", (string)$version->copy(2));
        $this->assertEquals("0.2.0-alpha+build", (string)$version->copy(null, 2));
        $this->assertEquals("0.0.2-alpha+build", (string)$version->copy(null, null, 2));
        $this->assertEquals("0.0.0-beta+build", (string)$version->copy(null, null, null, "beta"));
        $this->assertEquals("0.0.0-alpha+build2", (string)$version->copy(null, null, null, null, "build2"));
        $this->assertEquals("2.3.4-alpha+build", (string)$version->copy(2, 3, 4));
    }

    public function testCopyStable()
    {
        $version = Version::create(0, 0,0);
        $this->assertEquals($version, $version->copy());

        $this->assertEquals("2.0.0", (string)$version->copy(2));
        $this->assertEquals("0.2.0", (string)$version->copy(null, 2));
        $this->assertEquals("0.0.2", (string)$version->copy(null, null, 2));
        $this->assertEquals("0.0.0-beta", (string)$version->copy(null, null, null, "beta"));
        $this->assertEquals("0.0.0+build2", (string)$version->copy(null, null, null, null, "build2"));
        $this->assertEquals("2.3.4", (string)$version->copy(2, 3, 4));
    }

    public function testSuffixes()
    {
        $version = Version::parse("0.1.2-alpha+build");
        $this->assertEquals("0.1.2", (string)$version->withoutSuffixes());
    }
}