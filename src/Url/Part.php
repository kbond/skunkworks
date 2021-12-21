<?php

namespace Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Part implements \Stringable
{
    use Stringable;

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    final protected function generateString(): string
    {
        return $this->value;
    }
}
