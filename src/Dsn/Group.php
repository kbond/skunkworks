<?php

namespace Zenstruck\Dsn;

use Zenstruck\Uri\Query;
use Zenstruck\Uri\Scheme;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Group extends Wrapped
{
    /** @var \Stringable[] */
    private array $children;

    /**
     * @param \Stringable[] $children
     */
    public function __construct(Scheme $scheme, Query $query, array $children)
    {
        $this->children = $children;

        parent::__construct($scheme, $query);
    }

    /**
     * @return \Stringable[]
     */
    public function children(): array
    {
        return $this->children;
    }

    protected function innerString(): string
    {
        return \implode(' ', $this->children);
    }
}
