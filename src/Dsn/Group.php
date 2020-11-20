<?php

namespace Zenstruck\Dsn;

use Zenstruck\Url\Query;
use Zenstruck\Url\Scheme;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Group implements \Stringable
{
    private Scheme $scheme;
    private Query $query;

    /** @var \Stringable[] */
    private array $children;

    /**
     * @param \Stringable[] $children
     */
    public function __construct(Scheme $scheme, Query $query, array $children)
    {
        $this->scheme = $scheme;
        $this->query = $query;
        $this->children = $children;
    }

    public function __toString(): string
    {
        return \sprintf('%s(%s)', $this->scheme, \implode(' ', $this->children));
    }

    public function scheme(): Scheme
    {
        return $this->scheme;
    }

    public function query(): Query
    {
        return $this->query;
    }

    /**
     * @return \Stringable[]
     */
    public function children(): array
    {
        return $this->children;
    }
}
