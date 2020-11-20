<?php

namespace Zenstruck\Dsn;

use Zenstruck\Url\Query;
use Zenstruck\Url\Scheme;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Wrapped implements \Stringable
{
    private Scheme $scheme;
    private Query $query;

    public function __construct(Scheme $scheme, Query $query)
    {
        $this->scheme = $scheme;
        $this->query = $query;
    }

    final public function __toString(): string
    {
        return \sprintf(
            '%s(%s)%s',
            $this->scheme(),
            $this->innerString(),
            $this->query()->isEmpty() ? '' : "?{$this->query()}"
        );
    }

    final public function scheme(): Scheme
    {
        return $this->scheme;
    }

    final public function query(): Query
    {
        return $this->query;
    }

    abstract protected function innerString(): string;
}
