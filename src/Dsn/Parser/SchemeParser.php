<?php

namespace Zenstruck\Utilities\Dsn\Parser;

use Zenstruck\Utilities\Dsn\Parser;
use Zenstruck\Utilities\Dsn\Url\Scheme;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SchemeParser implements Parser
{
    public function parse(string $value): \Stringable
    {
        return new Scheme($value);
    }
}
