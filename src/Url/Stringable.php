<?php

namespace Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait Stringable
{
    public function __toString(): string
    {
        return $this->toString();
    }

    abstract public function toString(): string;
}
