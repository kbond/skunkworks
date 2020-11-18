<?php

namespace Zenstruck;

use Zenstruck\Dsn\Parser\ChainParser;
use Zenstruck\Dsn\Parser\GroupParser;
use Zenstruck\Dsn\Parser\MailtoParser;
use Zenstruck\Dsn\Parser\SchemeParser;
use Zenstruck\Dsn\Parser\UrlParser;

/**
 * Helper for parsing DSN objects provided by this component.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Dsn
{
    private static ?ChainParser $defaultParser = null;

    public static function parse(string $value): object
    {
        return self::defaultParser()->parse($value);
    }

    private static function defaultParser(): ChainParser
    {
        return self::$defaultParser ??= new ChainParser([
            new GroupParser(),
            new MailtoParser(),
            new UrlParser(),
            new SchemeParser(),
        ]);
    }
}
