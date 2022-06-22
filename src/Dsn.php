<?php

namespace Zenstruck;

use Zenstruck\Dsn\Parser\ChainParser;

/**
 * Helper for parsing DSN objects provided by this component.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Dsn
{
    private static ChainParser $defaultParser;

    public static function parse(string $value): \Stringable
    {
        return (self::$defaultParser ??= ChainParser::default())->parse($value);
    }
}
