<?php

namespace Zenstruck\Filesystem\Exception;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RuntimeException extends \RuntimeException
{
    public function __construct($message = '', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function wrap(\Throwable $exception, string $message, ...$parameters): self
    {
        if ($exception instanceof self) {
            return $exception;
        }

        return new self(\sprintf($message, ...$parameters), $exception);
    }
}
