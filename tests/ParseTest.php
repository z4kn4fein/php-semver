<?php

namespace z4kn4fein\SemVer\Tests;

use PHPUnit\Framework\TestCase;
use z4kn4fein\SemVer\SemverException;
use z4kn4fein\SemVer\Version;

class ParseTest extends TestCase
{
    /**
     * @dataProvider invalidData
     */
    public function testInvalid(string $version)
    {
        $this->expectException(SemverException::class);
        Version::parse($version);
    }

    /**
     * @dataProvider invalidData
     */
    public function testNullParse(string $version)
    {
        $this->assertNull(Version::parseOrNull($version));
    }

    /**
     * @dataProvider validData
     */
    public function testValid(string $version, bool $strict)
    {
        $this->assertNotNull(Version::parseOrNull($version, $strict));
    }

    /**
     * @dataProvider toStringData
     */
    public function testToString(string $expected, string $version, bool $strict)
    {
        $this->assertEquals($expected, (string) Version::parseOrNull($version, $strict));
    }

    public function testValidStable()
    {
        $version = Version::parse('1.2.3');
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(2, $version->getMinor());
        $this->assertEquals(3, $version->getPatch());
        $this->assertTrue($version->isStable());
        $this->assertFalse($version->isPreRelease());
    }

    public function testWithPreReleaseAndBuild()
    {
        $version = Version::parseOrNull('1.2.3-alpha.1+build.3.b');
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(2, $version->getMinor());
        $this->assertEquals(3, $version->getPatch());
        $this->assertFalse($version->isStable());
        $this->assertTrue($version->isPreRelease());
        $this->assertEquals('alpha.1', (string) $version->getPreRelease());
        $this->assertEquals('build.3.b', $version->getBuildMeta());
        $this->assertEquals('1.2.3-alpha.1+build.3.b', (string) $version);
    }

    public function testWithPreRelease()
    {
        $version = Version::parseOrNull('1.2.3-alpha.1.a.34');
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(2, $version->getMinor());
        $this->assertEquals(3, $version->getPatch());
        $this->assertFalse($version->isStable());
        $this->assertTrue($version->isPreRelease());
        $this->assertEquals('alpha.1.a.34', (string) $version->getPreRelease());
        $this->assertEmpty($version->getBuildMeta());
    }

    public function testWithBuild()
    {
        $version = Version::parseOrNull('1.2.3+build.3.b');
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(2, $version->getMinor());
        $this->assertEquals(3, $version->getPatch());
        $this->assertTrue($version->isStable());
        $this->assertFalse($version->isPreRelease());
        $this->assertEquals('build.3.b', $version->getBuildMeta());
        $this->assertEmpty($version->getPreRelease());
    }

    public function testNonStrict()
    {
        $this->assertEquals('1.2.3', (string) Version::parse('v1.2.3', false));
        $this->assertEquals('1.0.0', (string) Version::parse('v1', false));
        $this->assertEquals('1.0.0', (string) Version::parse('1', false));
        $this->assertEquals('1.2.0', (string) Version::parse('1.2', false));
        $this->assertEquals('1.2.0', (string) Version::parse('v1.2', false));

        $this->assertEquals('1.2.3-alpha+build', (string) Version::parse('v1.2.3-alpha+build', false));
        $this->assertEquals('1.0.0-alpha+build', (string) Version::parse('v1-alpha+build', false));
        $this->assertEquals('1.0.0-alpha+build', (string) Version::parse('1-alpha+build', false));
        $this->assertEquals('1.2.0-alpha+build', (string) Version::parse('1.2-alpha+build', false));
        $this->assertEquals('1.2.0-alpha+build', (string) Version::parse('v1.2-alpha+build', false));
    }

    public function testIsStable()
    {
        $this->assertFalse(Version::parse('0.1.2')->isStable());
        $this->assertFalse(Version::parse('1.1.0-prerelease')->isStable());
        $this->assertTrue(Version::parse('1.1.0')->isStable());
    }

    public function invalidData(): array
    {
        return [
            ['-1.0.0'],
            ['1.-1.0'],
            ['0.0.-1'],
            ['1'],
            [''],
            ['1.0'],
            ['1.0-alpha'],
            ['1.0-alpha.01'],
            ['a1.0.0'],
            ['1.a0.0'],
            ['1.0.a0'],
            ['v1.0.0'],
        ];
    }

    public function validData(): array
    {
        return [
            ['0.0.0', true],
            ['1.2.3-alpha.1+build', true],
            ['v1.0.0', false],
            ['1.0', false],
            ['v1', false],
            ['1', false],
        ];
    }

    public function toStringData(): array
    {
        return [
            ['1.2.3', '1.2.3', true],
            ['1.2.3-alpha.b.3', '1.2.3-alpha.b.3', true],
            ['1.2.3-alpha+build', '1.2.3-alpha+build', true],
            ['1.2.3+build', '1.2.3+build', true],
            ['1.2.3', 'v1.2.3', false],
            ['1.0.0', 'v1', false],
            ['1.0.0', '1', false],
            ['1.2.0', '1.2', false],
            ['1.2.0', 'v1.2', false],
            ['1.2.3-alpha+build', 'v1.2.3-alpha+build', false],
            ['1.0.0-alpha+build', 'v1-alpha+build', false],
            ['1.0.0-alpha+build', '1-alpha+build', false],
            ['1.2.0-alpha+build', '1.2-alpha+build', false],
            ['1.2.0-alpha+build', 'v1.2-alpha+build', false],
        ];
    }
}
