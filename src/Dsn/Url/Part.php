<?php

namespace Zenstruck\Utilities\Dsn\Url;

/**
 * @experimental
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Part implements \Stringable
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    final public function __toString(): string
    {
        return $this->value;
    }

    final public function value(): string
    {
        return $this->value;
    }
}
