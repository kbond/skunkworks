<?php

namespace Zenstruck\Dsn;

use Zenstruck\Url\Query;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Group implements \Stringable
{
    private string $name;

    /** @var \Stringable[] */
    private array $children;

    private Query $query;

    /**
     * @param \Stringable[] $children
     */
    public function __construct(string $name, array $children, Query $query)
    {
        $this->name = $name;
        $this->children = $children;
        $this->query = $query;
    }

    public function __toString(): string
    {
        return \sprintf('%s(%s)', $this->name, \implode(' ', $this->children));
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return \Stringable[]
     */
    public function children(): array
    {
        return $this->children;
    }

    public function query(): Query
    {
        return $this->query;
    }
}
