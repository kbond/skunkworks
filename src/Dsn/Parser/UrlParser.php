<?php

namespace Zenstruck\Dsn\Parser;

use Zenstruck\Dsn\Exception\UnableToParse;
use Zenstruck\Dsn\Parser;
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlParser implements Parser
{
    public function parse(string $value): Url
    {
        try {
            return Url::create($value);
        } catch (\InvalidArgumentException $e) {
            throw new UnableToParse($value, $e);
        }
    }
}
