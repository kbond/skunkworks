<?php

namespace Zenstruck\Utilities\Tests\Dsn\Parser;

use PHPUnit\Framework\TestCase;
use Zenstruck\Utilities\Dsn\Exception\UnableToParse;
use Zenstruck\Utilities\Dsn\Parser\ChainParser;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ChainParserTest extends TestCase
{
    /**
     * @test
     */
    public function throws_exception_if_unable_to_parse(): void
    {
        $this->expectException(UnableToParse::class);
        $this->expectExceptionMessage('Unable to parse "foo".');

        (new ChainParser([]))->parse('foo');
    }
}
