<?php

namespace Zenstruck\Filesystem\Exception;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class NotFound extends RuntimeException
{
    public static function forPath(string $path, ?\Throwable $previous = null): self
    {
        return new self("Path \"{$path}\" not found.", $previous);
    }
}
