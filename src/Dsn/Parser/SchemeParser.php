<?php

namespace Zenstruck\Utilities\Dsn\Parser;

use Zenstruck\Utilities\Dsn\Parser;
use Zenstruck\Utilities\Url\Scheme;

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
