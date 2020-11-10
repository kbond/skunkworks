<?php

namespace Zenstruck\Utilities\Tests;

use PHPUnit\Framework\TestCase;
use function Zenstruck\Utilities\Functions\mailto;
use function Zenstruck\Utilities\Functions\null_trim;
use function Zenstruck\Utilities\Functions\remove_whitespace;
use function Zenstruck\Utilities\Functions\sql_pattern;
use function Zenstruck\Utilities\Functions\sql_pattern_begins_with;
use function Zenstruck\Utilities\Functions\sql_pattern_contains;
use function Zenstruck\Utilities\Functions\sql_pattern_ends_with;
use function Zenstruck\Utilities\Functions\truncate_word;
use function Zenstruck\Utilities\Functions\url;
use function Zenstruck\Utilities\Functions\value;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FunctionsTest extends TestCase
{
    /**
     * @test
     */
    public function value(): void
    {
        $this->assertNull(value(null));
        $this->assertSame([], value([]));
        $this->assertSame('ret', value(function() { return 'ret'; }));
        $this->assertInstanceOf(InvokableObject::class, value(new InvokableObject()));
    }

    /**
     * @test
     * @dataProvider removeWhitespaceProvider
     */
    public function remove_whitespace($value, $expected): void
    {
        $this->assertSame($expected, remove_whitespace($value));
    }

    public static function removeWhitespaceProvider(): array
    {
        return [
            ['foo', 'foo'],
            ['foo    bar', 'foo bar'],
            ['foo &nbsp;   bar', 'foo bar'],
            ["  foo &nbsp;   \n\n\n  \r  bar", ' foo bar'],
        ];
    }

    /**
     * @test
     * @dataProvider nullTrimProvider
     */
    public function null_trim($value, $charlist, $expected): void
    {
        $this->assertSame($expected, null_trim($value, $charlist));
    }

    public static function nullTrimProvider(): array
    {
        return [
            [null, null, null],
            ['0', null, '0'],
            ['foo', null, 'foo'],
            ['  foo', null, 'foo'],
            ['foo  ', null, 'foo'],
            ['  foo  ', null, 'foo'],
            ['foo / ', '/ ', 'foo'],
            ['/  foo  ', ' /', 'foo'],
            ['', null, null],
            [' ', null, null],
            ['  ', null, null],
            [
                [' ', 'foo', null, '  foo', ['foo', '', ' ']],
                null,
                [null, 'foo', null, 'foo', ['foo', null, null]],
            ],
            [
                [' /', 'foo/', '/', '  /foo', ['foo /', '/', '   / ']],
                ' /',
                [null, 'foo', null, 'foo', ['foo', null, null]],
            ],
            [
                ['foo' => '   bar  ', 'bar' => '     '],
                null,
                ['foo' => 'bar', 'bar' => null],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider truncateWordProvider
     */
    public function truncate_word($value, $length, $suffix, $expected): void
    {
        $this->assertSame($expected, truncate_word($value, $length, $suffix));
    }

    public static function truncateWordProvider(): array
    {
        return [
            [null, 255, '', ''],
            ['', 255, '', ''],
            ['', 255, '...', ''],
            ['foo', 3, '...', 'foo'],
            ['foo', 2, '', ''],
            ['foo', 2, '...', ''],
            ['foo bar', 3, '', 'foo'],
            ['foo bar baz', 6, '', 'foo'],
            ['foo bar baz', 7, '', 'foo bar'],
            ['foo bar baz', 7, '...', 'foo...'],
            ['foo bar baz', 9, '...', 'foo...'],
            ['foo bar baz', 10, '...', 'foo bar...'],
            ['foo bar baz', 11, '...', 'foo bar baz'],
            ['foo bar baz bob', 11, '...', 'foo bar...'],
            ['foo bar baz', 12, '...', 'foo bar baz'],
            ['      foo       bar  baz', 10, '...', 'foo bar...'],
        ];
    }

    /**
     * @test
     */
    public function sql_pattern(): void
    {
        $this->assertSame('foo', sql_pattern('foo'));
        $this->assertSame('f%o', sql_pattern('f*o'));
    }

    /**
     * @test
     */
    public function sql_pattern_contains(): void
    {
        $this->assertSame('%foo%', sql_pattern_contains('foo'));
        $this->assertSame('%f%o%', sql_pattern_contains('f*o'));
    }

    /**
     * @test
     */
    public function sql_pattern_begins_with(): void
    {
        $this->assertSame('foo%', sql_pattern_begins_with('foo'));
        $this->assertSame('f%o%', sql_pattern_begins_with('f*o'));
    }

    /**
     * @test
     */
    public function sql_pattern_ends_with(): void
    {
        $this->assertSame('%foo', sql_pattern_ends_with('foo'));
        $this->assertSame('%f%o', sql_pattern_ends_with('f*o'));
    }

    /**
     * @test
     */
    public function url(): void
    {
        $this->assertSame('example.com', (string) url('https://example.com:8080/foo')->host());
    }

    /**
     * @test
     */
    public function mailto(): void
    {
        $this->assertSame('subject', mailto('kevin@example.com?subject=subject')->subject());
    }
}

final class InvokableObject
{
    public function __invoke(): string
    {
        return 'invoked';
    }
}
