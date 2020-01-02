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
        $preRelease = PreRelease::parse("alpha-3.Beta.13");
        $this->assertNotNull($preRelease);
    }

    public function testIncrementWithNumeric()
    {
        $preRelease = PreRelease::parse("alpha-3.13.Beta");
        $incremented = $preRelease->increment();
        $this->assertEquals("alpha-3.14.Beta", (string)$incremented);
    }

    public function testIncrementWithMultipleNumeric()
    {
        $preRelease = PreRelease::parse("alpha.5.Beta.7");
        $incremented = $preRelease->increment();
        $this->assertEquals("alpha.5.Beta.8", (string)$incremented);
    }
}