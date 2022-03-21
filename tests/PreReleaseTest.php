<?php

namespace z4kn4fein\SemVer\Tests;

use PHPUnit\Framework\TestCase;
use z4kn4fein\SemVer\PreRelease;
use z4kn4fein\SemVer\VersionFormatException;

class PreReleaseTest extends TestCase
{
    public function testEmpty()
    {
        $this->expectException(VersionFormatException::class);
        PreRelease::parse("");
    }

    public function testEmptyPart()
    {
        $this->expectException(VersionFormatException::class);
        PreRelease::parse("alpha.");
    }

    public function testWhitespace()
    {
        $this->expectException(VersionFormatException::class);
        PreRelease::parse(" ");
    }

    public function testWhitespacePart()
    {
        $this->expectException(VersionFormatException::class);
        PreRelease::parse("alpha. ");
    }

    public function testInvalid()
    {
        $this->expectException(VersionFormatException::class);
        PreRelease::parse("alpha$");
    }

    public function testInvalidNumeric()
    {
        $this->expectException(VersionFormatException::class);
        PreRelease::parse("alpha.012");
    }

    public function testValid()
    {
        $this->assertNotNull(PreRelease::parse("0alpha-3.Beta.13"));
    }

    public function testValidAlphaNumeric()
    {
        $this->assertNotNull(PreRelease::parse("0alpha"));
    }

    public function testIncrementWithoutNumeric()
    {
        $this->assertEquals("alpha-3.Beta.0", (string)PreRelease::parse("alpha-3.Beta")->increment());
    }

    public function testIncrementWithNumeric()
    {
        $this->assertEquals("alpha-3.14.Beta", (string)PreRelease::parse("alpha-3.13.Beta")->increment());
    }

    public function testIncrementWithMultipleNumeric()
    {
        $this->assertEquals("alpha.5.Beta.8", (string)PreRelease::parse("alpha.5.Beta.7")->increment());
    }
}