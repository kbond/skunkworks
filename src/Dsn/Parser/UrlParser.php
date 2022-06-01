<?php

namespace Zenstruck\Dsn\Parser;

use Zenstruck\Dsn\Exception\UnableToParse;
use Zenstruck\Dsn\Parser;
use Zenstruck\Uri;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlParser implements Parser
{
    public function parse(string $value): Uri
    {
        try {
            return Uri::new($value);
        } catch (\InvalidArgumentException $e) {
            throw new UnableToParse($value, $e);
        }
    }
}
