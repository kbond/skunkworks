<?php

namespace Zenstruck\Dimension\Tests;

use PHPUnit\Framework\TestCase;

use function Zenstruck\dimension;
use function Zenstruck\Dimension\humanize_bytes;
use function Zenstruck\Dimension\humanize_bytes_binary;
use function Zenstruck\Dimension\humanize_duration;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FunctionsTest extends TestCase
{
    /**
     * @test
     */
    public function dimension(): void
    {
        $this->assertSame(22.0, dimension('22mm')->quantity());
    }

    /**
     * @test
     * @dataProvider humanizeBytesProvider
     */
    public function humanize_bytes($value, $expected): void
    {
        $this->assertSame($expected, humanize_bytes($value));
    }

    public function humanizeBytesProvider(): iterable
    {
        yield [1000, '1 kB'];
        yield [1100, '1.1 kB'];
        yield ['1100B', '1.1 kB'];
        yield [dimension(1.1, 'kB'), '1.1 kB'];
        yield [dimension(0.0011, 'MB'), '1.1 kB'];
        yield [dimension(0.0011, 'MiB'), '1.15 kB'];
        yield [0, '0 B'];
        yield [32, '32 B'];
        yield [1000 ** 8, '1 YB'];
        yield [1000 ** 9, '1,000 YB'];
        yield [1000 ** 10, '1,000,000 YB'];
    }

    /**
     * @test
     * @dataProvider humanizeBytesBinaryProvider
     */
    public function humanize_bytes_binary($value, $expected): void
    {
        $this->assertSame($expected, humanize_bytes_binary($value));
    }

    public function humanizeBytesBinaryProvider(): iterable
    {
        yield [1024, '1 KiB'];
        yield [1050, '1.03 KiB'];
        yield [1130, '1.1 KiB'];
        yield ['1130B', '1.1 KiB'];
        yield [dimension(1.1, 'kB'), '1.07 KiB'];
        yield [dimension(0.0011, 'MB'), '1.07 KiB'];
        yield [dimension(0.0011, 'MiB'), '1.13 KiB'];
        yield [0, '0 B'];
        yield [32, '32 B'];
        yield [1024 ** 8, '1 YiB'];
        yield [1024 ** 9, '1,024 YiB'];
        yield [1024 ** 10, '1,048,576 YiB'];
    }

    /**
     * @test
     * @dataProvider humanizeDurationProvider
     */
    public function humanize_duration($value, $expected): void
    {
        $this->assertSame($expected, humanize_duration($value));
    }

    public static function humanizeDurationProvider()
    {
        return [
            [0, '< 1 sec'],
            [0.3, '< 1 sec'],
            [1, '1 sec'],
            [2, '2 secs'],
            [59, '59 secs'],
            [60, '1 min'],
            ['1m', '1 min'],
            [dimension('1', 'min'), '1 min'],
            [dimension(1.5, 'min'), '1 min'],
            [61, '1 min'],
            [119, '1 min'],
            [120, '2 mins'],
            [121, '2 mins'],
            [3599, '59 mins'],
            [3600, '1 hr'],
            [7199, '1 hr'],
            [7200, '2 hrs'],
            [7201, '2 hrs'],
            [86399, '23 hrs'],
            [86400, '1 day'],
            [86401, '1 day'],
            [172799, '1 day'],
            [172800, '2 days'],
            [172801, '2 days'],
        ];
    }
}
