<?php

namespace Zenstruck\Utilities\Dsn\Parser;

use Zenstruck\Utilities\Dsn\Parser;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface ParserAware
{
    public function setParser(Parser $parser): void;
}
