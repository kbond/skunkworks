<?php

namespace Zenstruck\Dsn\Parser;

use Zenstruck\Dsn\Decorated;
use Zenstruck\Dsn\Exception\UnableToParse;
use Zenstruck\Dsn\Group;
use Zenstruck\Dsn\Parser;
use Zenstruck\Dsn\Wrapped;
use Zenstruck\Url\Query;
use Zenstruck\Url\Scheme;

/**
 * Parses strings like "name(dsn1 dsn2)" into a Group dsn.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class WrappedParser implements Parser, ParserAware
{
    private ?Parser $parser = null;

    /**
     * @return Group|Decorated
     */
    public function parse(string $value): Wrapped
    {
        if (!\preg_match('#^([\w+]+)\((.+)\)(\?.+)?$#', $value, $matches)) {
            throw new UnableToParse($value);
        }

        $scheme = new Scheme($matches[1]);
        $query = new Query($matches[3] ?? '');

        if (1 === \count(\explode(' ', $matches[2]))) {
            return new Decorated($scheme, $query, $this->parser()->parse($matches[2]));
        }

        return new Group(
            $scheme,
            $query,
            \array_map(function(string $dsn) { return $this->parser()->parse($dsn); }, self::explode($matches[2]))
        );
    }

    public function setParser(Parser $parser): void
    {
        $this->parser = $parser;
    }

    private function parser(): Parser
    {
        if (!$this->parser) {
            throw new \RuntimeException('Parser not set.');
        }

        return $this->parser;
    }

    /**
     * Explodes the groups by space but keeps nested groups together.
     */
    private static function explode(string $value): array
    {
        $nest = 0;
        $parts = [];
        $part = '';

        foreach (\mb_str_split($value) as $char) {
            if (' ' === $char && 0 === $nest) {
                $parts[] = $part;
                $part = '';

                continue;
            }

            if ('(' === $char) {
                ++$nest;
            }

            if (')' === $char) {
                --$nest;
            }

            $part .= $char;
        }

        $parts[] = $part;

        return $parts;
    }
}
