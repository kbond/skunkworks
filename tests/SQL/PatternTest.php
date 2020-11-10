<?php

namespace Zenstruck\Utilities\Tests\SQL;

use PHPUnit\Framework\TestCase;
use Zenstruck\Utilities\SQL\Pattern;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class PatternTest extends TestCase
{
    /**
     * @test
     * @dataProvider patternProvider
     */
    public function pattern(Pattern $pattern, $expected): void
    {
        $this->assertSame($expected, (string) $pattern);
    }

    public static function patternProvider(): iterable
    {
        yield [new Pattern(null), ''];
        yield [new Pattern('foo'), 'foo'];
        yield [new Pattern('%'), '%'];
        yield [new Pattern('%fo%o%'), '%fo%o%'];
        yield [new Pattern('%fo*o%'), '%fo*o%'];
        yield [new Pattern('%fo*o%', '*'), '%fo%o%'];
        yield [new Pattern('%fo&o%', '&'), '%fo%o%'];

        yield [Pattern::contains(null), '%%'];
        yield [Pattern::contains('foo'), '%foo%'];
        yield [Pattern::contains('%'), '%%%'];
        yield [Pattern::contains('fo%o'), '%fo%o%'];
        yield [Pattern::contains('fo*o'), '%fo*o%'];
        yield [Pattern::contains('fo*o', '*'), '%fo%o%'];
        yield [Pattern::contains('fo&o', '&'), '%fo%o%'];

        yield [Pattern::beginsWith(null), '%'];
        yield [Pattern::beginsWith('foo'), 'foo%'];
        yield [Pattern::beginsWith('%'), '%%'];
        yield [Pattern::beginsWith('fo%o'), 'fo%o%'];
        yield [Pattern::beginsWith('fo*o'), 'fo*o%'];
        yield [Pattern::beginsWith('fo*o', '*'), 'fo%o%'];
        yield [Pattern::beginsWith('fo&o', '&'), 'fo%o%'];

        yield [Pattern::endsWith(null), '%'];
        yield [Pattern::endsWith('foo'), '%foo'];
        yield [Pattern::endsWith('%'), '%%'];
        yield [Pattern::endsWith('fo%o'), '%fo%o'];
        yield [Pattern::endsWith('fo*o'), '%fo*o'];
        yield [Pattern::endsWith('fo*o', '*'), '%fo%o'];
        yield [Pattern::endsWith('fo&o', '&'), '%fo%o'];
    }
}
