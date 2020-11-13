<?php

namespace Zenstruck\Dsn\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Zenstruck\Dsn\Parser\GroupParser;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class GroupParserTest extends TestCase
{
    /**
     * @test
     */
    public function throws_exception_if_parser_is_not_set(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Parser not set.');

        (new GroupParser())->parse('failover(foo)');
    }
}
