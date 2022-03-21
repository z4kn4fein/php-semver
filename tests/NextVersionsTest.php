<?php

namespace z4kn4fein\SemVer\Tests;

use PHPUnit\Framework\TestCase;
use z4kn4fein\SemVer\Version;

class NextVersionsTest extends TestCase
{
    public function testNextVersions()
    {
        $version = Version::parse("1.2.3-alpha.4+build.3");

        $this->assertEquals("2.0.0", (string)$version->getNextMajorVersion());
        $this->assertEquals("1.3.0", (string)$version->getNextMinorVersion());
        $this->assertEquals("1.2.3", (string)$version->getNextPatchVersion());
        $this->assertEquals("1.2.3-alpha.5", (string)$version->getNextPreReleaseVersion());
    }

    public function testNextVersionsWithoutPreRelease()
    {
        $version = Version::parse("1.2.3");

        $this->assertEquals("2.0.0", (string)$version->getNextMajorVersion());
        $this->assertEquals("1.3.0", (string)$version->getNextMinorVersion());
        $this->assertEquals("1.2.4", (string)$version->getNextPatchVersion());
        $this->assertEquals("1.2.4-0", (string)$version->getNextPreReleaseVersion());
    }
}