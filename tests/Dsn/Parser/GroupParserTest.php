<?php

namespace Zenstruck\Utilities\Tests\Dsn\Parser;

use PHPUnit\Framework\TestCase;
use Zenstruck\Utilities\Dsn\Parser\GroupParser;

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
