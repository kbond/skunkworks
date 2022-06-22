<?php

namespace Zenstruck\Dsn\Parser;

use Symfony\Contracts\Cache\CacheInterface;
use Zenstruck\Dsn\Parser;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CacheParser implements Parser
{
    private Parser $parser;
    private CacheInterface $cache;

    public function __construct(Parser $parser, CacheInterface $cache)
    {
        $this->parser = $parser;
        $this->cache = $cache;
    }

    public function parse(string $value): \Stringable
    {
        return $this->cache->get('dsn-'.\md5($value), fn() => $this->parser->parse($value));
    }
}
