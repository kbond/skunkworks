<?php

namespace Zenstruck\Utilities\Dsn\Parser;

use Zenstruck\Utilities\Dsn\Exception\UnableToParse;
use Zenstruck\Utilities\Dsn\Parser;
use Zenstruck\Utilities\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlParser implements Parser
{
    public function parse(string $value): Url
    {
        try {
            return new Url($value);
        } catch (\InvalidArgumentException $e) {
            throw new UnableToParse($value, $e);
        }
    }
}
