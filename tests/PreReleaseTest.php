<?php

namespace z4kn4fein\SemVer\Tests;

use z4kn4fein\SemVer\PreRelease;
use z4kn4fein\SemVer\VersionFormatException;

class PreReleaseTest extends \PHPUnit_Framework_TestCase
{
    public function testNull()
    {
        $this->setExpectedException(VersionFormatException::class);
        PreRelease::parse(null);
    }

    public function testEmpty()
    {
        $this->setExpectedException(VersionFormatException::class);
        PreRelease::parse("");
    }

    public function testWhitespace()
    {
        $this->setExpectedException(VersionFormatException::class);
        PreRelease::parse(" ");
    }

    public function testInvalid()
    {
        $this->setExpectedException(VersionFormatException::class);
        PreRelease::parse("alpha$");
    }

    public function testInvalidNumeric()
    {
        $this->setExpectedException(VersionFormatException::class);
        PreRelease::parse("alpha.012");
    }

    public function testValid()
    {
        $this->assertNotNull(PreRelease::parse("0alpha-3.Beta.13"));
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