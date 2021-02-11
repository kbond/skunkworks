<?php

namespace Zenstruck;

use Zenstruck\Dsn\Parser;
use Zenstruck\Dsn\Parser\ChainParser;
use Zenstruck\Dsn\Parser\MailtoParser;
use Zenstruck\Dsn\Parser\UrlParser;
use Zenstruck\Dsn\Parser\WrappedParser;

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

    public static function defaultParser(): Parser
    {
        return self::$defaultParser ??= new ChainParser([
            new WrappedParser(),
            new MailtoParser(),
            new UrlParser(),
        ]);
    }
}
