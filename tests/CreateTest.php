<?php

namespace z4kn4fein\SemVer\Tests;

use z4kn4fein\SemVer\Version;
use z4kn4fein\SemVer\VersionFormatException;

class CreateTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidMajor()
    {
        $this->setExpectedException(VersionFormatException::class);
        Version::create(-1, 0,0);
    }

    public function testInvalidMinor()
    {
        $this->setExpectedException(VersionFormatException::class);
        Version::create(0, -1,0);
    }

    public function testInvalidPatch()
    {
        $this->setExpectedException(VersionFormatException::class);
        Version::create(0, 0,-1);
    }

    public function testAllZeros()
    {
        $this->assertNotNull(Version::create(0, 0,0));
    }
}