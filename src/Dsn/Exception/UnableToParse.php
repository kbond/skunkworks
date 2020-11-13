<?php

namespace Zenstruck\Dsn\Exception;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UnableToParse extends \RuntimeException
{
    public function __construct(string $value, ?\Throwable $previous = null)
    {
        parent::__construct("Unable to parse \"{$value}\".", 0, $previous);
    }
}
