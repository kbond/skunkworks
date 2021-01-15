<?php

namespace Zenstruck\Filesystem\Node;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Exception\RuntimeException;
use Zenstruck\Filesystem\Exception\UnsupportedFeature;
use Zenstruck\Filesystem\Node;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Directory extends Node implements \IteratorAggregate
{
    private bool $recursive = false;

    /**
     * @return Node[]
     *
     * @throws RuntimeException
     */
    public function getIterator(): \Traversable
    {
        try {
            foreach ($this->adapter->listing($this->path()) as $path => $type) {
                if (Adapter::TYPE_FILE === $type) {
                    yield $path => self::file($this->adapter, $path);

                    continue;
                }

                yield $path => $dir = self::directory($this->adapter, $path);

                if (!$this->recursive) {
                    continue;
                }

                yield from $dir->recursive();
            }
        } catch (\Throwable $e) {
            throw RuntimeException::wrap($e, 'Failed to list directory contents for "%s".', $this->path());
        }
    }

    /**
     * @return File[]
     */
    public function files(): \Traversable
    {
        foreach ($this as $node) {
            if ($node->isFile()) {
                yield $node->path() => $node;
            }
        }
    }

    /**
     * @return self[]
     */
    public function directories(): \Traversable
    {
        foreach ($this as $node) {
            if ($node->isDirectory()) {
                yield $node->path() => $node;
            }
        }
    }

    public function recursive(): self
    {
        $clone = clone $this;
        $clone->recursive = true;

        return $clone;
    }

    /**
     * @throws UnsupportedFeature If adapter does not support accessing a real directory
     * @throws RuntimeException   If unable to access real directory
     */
    public function real(): \SplFileInfo
    {
        try {
            return $this->adapter->realDirectory($this->path());
        } catch (UnsupportedFeature $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw RuntimeException::wrap($e, 'Unable to access real directory "%s".', $this->path());
        }
    }
}
