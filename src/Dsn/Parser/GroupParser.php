<?php

namespace Zenstruck\Utilities\Dsn\Parser;

use Zenstruck\Utilities\Dsn\Exception\UnableToParse;
use Zenstruck\Utilities\Dsn\Group;
use Zenstruck\Utilities\Dsn\Parser;

/**
 * Parses strings like "name(dsn1 dsn2)" into a Group dsn.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class GroupParser implements Parser, ParserAware
{
    /** @var Parser|null */
    private $parser;

    public function parse(string $value): \Stringable
    {
        if (!\preg_match('#^(\w+)\((.+)\)$#', $value, $matches)) {
            throw new UnableToParse($value);
        }

        return new Group(
            $matches[1],
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
