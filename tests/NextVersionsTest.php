<?php

namespace z4kn4fein\SemVer\Tests;

use PHPUnit\Framework\TestCase;
use z4kn4fein\SemVer\Inc;
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

    /**
     * @dataProvider data
     */
    public function testInc(string $source, int $incBy, string $expected, ?string $preRelease)
    {
        $this->assertEquals($expected, (string)Version::parse($source)->inc($incBy, $preRelease));
    }

    public function data(): array
    {
        return [
            ["1.2.3", Inc::MAJOR, "2.0.0", null],
            ["1.2.3", Inc::MINOR, "1.3.0", null],
            ["1.2.3", Inc::PATCH, "1.2.4", null],
            ["1.2.3-alpha", Inc::MAJOR, "2.0.0", null],
            ["1.2.0-0", Inc::PATCH, "1.2.0", null],
            ["1.2.3-4", Inc::MAJOR, "2.0.0", null],
            ["1.2.3-4", Inc::MINOR, "1.3.0", null],
            ["1.2.3-4", Inc::PATCH, "1.2.3", null],
            ["1.2.3-alpha.0.beta", Inc::MAJOR, "2.0.0", null],
            ["1.2.3-alpha.0.beta", Inc::MINOR, "1.3.0", null],
            ["1.2.3-alpha.0.beta", Inc::PATCH, "1.2.3", null],
            ["1.2.4", Inc::PRE_RELEASE, "1.2.5-0", null],
            ["1.2.3-0", Inc::PRE_RELEASE, "1.2.3-1", null],
            ["1.2.3-alpha.0", Inc::PRE_RELEASE, "1.2.3-alpha.1", null],
            ["1.2.3-alpha.1", Inc::PRE_RELEASE, "1.2.3-alpha.2", null],
            ["1.2.3-alpha.2", Inc::PRE_RELEASE, "1.2.3-alpha.3", null],
            ["1.2.3-alpha.0.beta", Inc::PRE_RELEASE, "1.2.3-alpha.1.beta", null],
            ["1.2.3-alpha.1.beta", Inc::PRE_RELEASE, "1.2.3-alpha.2.beta", null],
            ["1.2.3-alpha.2.beta", Inc::PRE_RELEASE, "1.2.3-alpha.3.beta", null],
            ["1.2.3-alpha.10.0.beta", Inc::PRE_RELEASE, "1.2.3-alpha.10.1.beta", null],
            ["1.2.3-alpha.10.1.beta", Inc::PRE_RELEASE, "1.2.3-alpha.10.2.beta", null],
            ["1.2.3-alpha.10.2.beta", Inc::PRE_RELEASE, "1.2.3-alpha.10.3.beta", null],
            ["1.2.3-alpha.10.beta.0", Inc::PRE_RELEASE, "1.2.3-alpha.10.beta.1", null],
            ["1.2.3-alpha.10.beta.1", Inc::PRE_RELEASE, "1.2.3-alpha.10.beta.2", null],
            ["1.2.3-alpha.10.beta.2", Inc::PRE_RELEASE, "1.2.3-alpha.10.beta.3", null],
            ["1.2.3-alpha.9.beta", Inc::PRE_RELEASE, "1.2.3-alpha.10.beta", null],
            ["1.2.3-alpha.10.beta", Inc::PRE_RELEASE, "1.2.3-alpha.11.beta", null],
            ["1.2.3-alpha.11.beta", Inc::PRE_RELEASE, "1.2.3-alpha.12.beta", null],
            ["1.2.0", Inc::PATCH, "1.2.1-0", ""],
            ["1.2.0-1", Inc::PATCH, "1.2.1-0", ""],
            ["1.2.0", Inc::MINOR, "1.3.0-0", ""],
            ["1.2.3-1", Inc::MINOR, "1.3.0-0", ""],
            ["1.2.0", Inc::MAJOR, "2.0.0-0", ""],
            ["1.2.3-1", Inc::MAJOR, "2.0.0-0", ""],

            ["1.2.4", Inc::PRE_RELEASE, "1.2.5-dev", "dev"],
            ["1.2.3-0", Inc::PRE_RELEASE, "1.2.3-dev", "dev"],
            ["1.2.3-alpha.0", Inc::PRE_RELEASE, "1.2.3-dev", "dev"],
            ["1.2.3-alpha.0", Inc::PRE_RELEASE, "1.2.3-alpha.1", "alpha"],
            ["1.2.3-alpha.0.beta", Inc::PRE_RELEASE, "1.2.3-dev", "dev"],
            ["1.2.3-alpha.0.beta", Inc::PRE_RELEASE, "1.2.3-alpha.1.beta", "alpha"],
            ["1.2.3-alpha.10.0.beta", Inc::PRE_RELEASE, "1.2.3-dev", "dev"],
            ["1.2.3-alpha.10.0.beta", Inc::PRE_RELEASE, "1.2.3-alpha.10.1.beta", "alpha"],
            ["1.2.3-alpha.10.1.beta", Inc::PRE_RELEASE, "1.2.3-alpha.10.2.beta", "alpha"],
            ["1.2.3-alpha.10.2.beta", Inc::PRE_RELEASE, "1.2.3-alpha.10.3.beta", "alpha"],
            ["1.2.3-alpha.10.beta.0", Inc::PRE_RELEASE, "1.2.3-dev", "dev"],
            ["1.2.3-alpha.10.beta.0", Inc::PRE_RELEASE, "1.2.3-alpha.10.beta.1", "alpha"],
            ["1.2.3-alpha.10.beta.1", Inc::PRE_RELEASE, "1.2.3-alpha.10.beta.2", "alpha"],
            ["1.2.3-alpha.10.beta.2", Inc::PRE_RELEASE, "1.2.3-alpha.10.beta.3", "alpha"],
            ["1.2.3-alpha.9.beta", Inc::PRE_RELEASE, "1.2.3-dev", "dev"],
            ["1.2.3-alpha.9.beta", Inc::PRE_RELEASE, "1.2.3-alpha.10.beta", "alpha"],
            ["1.2.3-alpha.10.beta", Inc::PRE_RELEASE, "1.2.3-alpha.11.beta", "alpha"],
            ["1.2.3-alpha.11.beta", Inc::PRE_RELEASE, "1.2.3-alpha.12.beta", "alpha"],
            ["1.2.0", Inc::PATCH, "1.2.1-dev", "dev"],
            ["1.2.0-1", Inc::PATCH, "1.2.1-dev", "dev"],
            ["1.2.0", Inc::MINOR, "1.3.0-dev", "dev"],
            ["1.2.3-1", Inc::MINOR, "1.3.0-dev", "dev"],
            ["1.2.0", Inc::MAJOR, "2.0.0-dev", "dev"],
            ["1.2.3-1", Inc::MAJOR, "2.0.0-dev", "dev"],
            ["1.2.0-1", Inc::MINOR, "1.3.0", null],
            ["1.0.0-1", Inc::MAJOR, "2.0.0", null],
            ["1.2.3-dev.beta", Inc::PRE_RELEASE, "1.2.3-dev.beta.0", "dev"],
        ];
    }
}