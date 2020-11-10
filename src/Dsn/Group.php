<?php

namespace Zenstruck\Utilities\Dsn;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Group implements \Stringable
{
    /** @var string */
    private $name;

    /** @var \Stringable[] */
    private $children;

    /**
     * @param \Stringable[] $children
     */
    public function __construct(string $name, array $children)
    {
        $this->name = $name;
        $this->children = $children;
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
}
