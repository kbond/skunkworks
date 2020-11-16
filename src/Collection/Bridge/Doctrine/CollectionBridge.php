<?php

namespace Zenstruck\Collection\Bridge\Doctrine;

use Doctrine\Common\Collections\Collection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait CollectionBridge
{
    /**
     * @return true
     */
    public function add($element): bool
    {
        return $this->doctrineCollection()->add($element);
    }

    public function clear(): void
    {
        $this->doctrineCollection()->clear();
    }

    public function contains($element): bool
    {
        return $this->doctrineCollection()->contains($element);
    }

    public function isEmpty(): bool
    {
        return $this->doctrineCollection()->isEmpty();
    }

    public function remove($key)
    {
        return $this->doctrineCollection()->remove($key);
    }

    public function removeElement($element): bool
    {
        return $this->doctrineCollection()->removeElement($element);
    }

    public function containsKey($key): bool
    {
        return $this->doctrineCollection()->containsKey($key);
    }

    public function get($key)
    {
        return $this->doctrineCollection()->get($key);
    }

    public function getKeys(): array
    {
        return $this->doctrineCollection()->getKeys();
    }

    public function getValues(): array
    {
        return $this->doctrineCollection()->getValues();
    }

    public function set($key, $value): void
    {
        $this->doctrineCollection()->set($key, $value);
    }

    public function toArray(): array
    {
        return $this->doctrineCollection()->toArray();
    }

    public function first()
    {
        return $this->doctrineCollection()->first();
    }

    public function last()
    {
        return $this->doctrineCollection()->last();
    }

    public function key()
    {
        return $this->doctrineCollection()->key();
    }

    public function current()
    {
        return $this->doctrineCollection()->current();
    }

    public function next()
    {
        return $this->doctrineCollection()->next();
    }

    public function exists(\Closure $p): bool
    {
        return $this->doctrineCollection()->exists($p);
    }

    public function filter(\Closure $p): Collection
    {
        return $this->doctrineCollection()->filter($p);
    }

    public function forAll(\Closure $p): bool
    {
        return $this->doctrineCollection()->forAll($p);
    }

    public function map(\Closure $func): Collection
    {
        return $this->doctrineCollection()->map($func);
    }

    public function partition(\Closure $p): array
    {
        return $this->doctrineCollection()->partition($p);
    }

    public function indexOf($element)
    {
        return $this->doctrineCollection()->indexOf($element);
    }

    public function slice($offset, $length = null): array
    {
        return $this->doctrineCollection()->slice($offset, $length);
    }

    public function offsetExists($offset): bool
    {
        return $this->doctrineCollection()->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->doctrineCollection()->offsetGet($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->doctrineCollection()->offsetSet($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->doctrineCollection()->offsetUnset($offset);
    }

    abstract protected function doctrineCollection(): Collection;
}
