<?php

namespace Zenstruck\Dsn\Parser;

use Zenstruck\Dsn\Exception\UnableToParse;
use Zenstruck\Dsn\Parser;
use Zenstruck\Mailto;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MailtoParser implements Parser
{
    public function parse(string $value): Mailto
    {
        if (0 === \mb_strpos($value, 'mailto:')) {
            return Mailto::new($value);
        }

        throw new UnableToParse($value);
    }
}
