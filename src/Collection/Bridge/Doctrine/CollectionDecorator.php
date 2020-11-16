<?php

namespace Zenstruck\Collection\Bridge\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Zenstruck\Collection;
use Zenstruck\Collection\Paginatable;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CollectionDecorator implements Collection, DoctrineCollection
{
    use Paginatable, CollectionBridge;

    private DoctrineCollection $inner;

    /**
     * @param iterable|DoctrineCollection|null $source
     */
    public function __construct($source = [])
    {
        $source = $source ?? [];

        if (!\is_iterable($source)) {
            throw new \InvalidArgumentException(); // todo
        }

        if (!$source instanceof DoctrineCollection) {
            $source = new ArrayCollection(\is_array($source) ? $source : \iterator_to_array($source));
        }

        $this->inner = $source;
    }

    public function take(int $limit, int $offset = 0): self
    {
        return new self($this->slice($offset, $limit));
    }

    public function count(): int
    {
        return $this->inner->count();
    }

    public function getIterator(): \Traversable
    {
        return $this->inner->getIterator();
    }

    protected function doctrineCollection(): DoctrineCollection
    {
        return $this->inner;
    }
}
