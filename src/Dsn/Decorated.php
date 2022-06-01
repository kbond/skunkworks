<?php

namespace Zenstruck\Dsn;

use Zenstruck\Uri\Query;
use Zenstruck\Uri\Scheme;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Decorated extends Wrapped
{
    private \Stringable $inner;

    public function __construct(Scheme $scheme, Query $query, \Stringable $inner)
    {
        $this->inner = $inner;

        parent::__construct($scheme, $query);
    }

    public function inner(): \Stringable
    {
        return $this->inner;
    }

    protected function innerString(): string
    {
        return $this->inner();
    }
}
