<?php

namespace Zenstruck\Dsn;

use Zenstruck\Url\Query;
use Zenstruck\Url\Scheme;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Decorated extends Wrapped
{
    private \Stringable $inner;

    /**
     * @param \Stringable[] $children
     */
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
