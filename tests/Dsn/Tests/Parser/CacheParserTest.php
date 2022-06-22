<?php

namespace Zenstruck\Dsn\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Zenstruck\Dsn\Exception\UnableToParse;
use Zenstruck\Dsn\Parser\CacheParser;
use Zenstruck\Dsn\Parser\ChainParser;
use Zenstruck\Uri\Mailto;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CacheParserTest extends TestCase
{
    /**
     * @test
     */
    public function throws_exception_if_unable_to_parse(): void
    {
        $cache = new ArrayAdapter();
        $parser = new CacheParser(new ChainParser([]), $cache);

        try {
            $parser->parse('foo');
        } catch (UnableToParse $e) {
        }

        $this->expectException(UnableToParse::class);

        $parser->parse('foo');
    }

    /**
     * @test
     */
    public function can_cache_result(): void
    {
        $cache = new ArrayAdapter();
        $parser = new CacheParser(ChainParser::default(), $cache);

        $parsed1 = $parser->parse('mailto:sally@example.com');
        $parsed2 = $parser->parse('mailto:sally@example.com');

        $this->assertEquals($parsed1, $parsed2);

        $item = $cache->getItem('dsn-'.\md5('mailto:sally@example.com'));
        $new = Mailto::new('john@example.com');
        $item->set($new);
        $cache->save($item);

        $this->assertEquals($new, $parser->parse('mailto:sally@example.com'));
    }
}
