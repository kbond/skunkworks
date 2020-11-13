<?php

namespace Zenstruck\Dsn\Parser;

use Zenstruck\Dsn\Parser;
use Zenstruck\Url\Scheme;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SchemeParser implements Parser
{
    public function parse(string $value): Scheme
    {
        return new Scheme($value);
    }
}
