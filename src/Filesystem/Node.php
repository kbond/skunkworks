<?php

namespace Zenstruck\Filesystem;

use Zenstruck\Filesystem\Adapter\AdapterWrapper;
use Zenstruck\Filesystem\Exception\UnsupportedFeature;
use Zenstruck\Filesystem\Node\Directory;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Node
{
    protected AdapterWrapper $adapter;
    private string $path;

    private function __construct(AdapterWrapper $adapter, string $path)
    {
        $this->adapter = $adapter;
        $this->path = $path;
    }

    final public function __toString(): string
    {
        return $this->path();
    }

    /**
     * @internal
     */
    final public static function file(AdapterWrapper $adapter, string $path): File
    {
        return new File($adapter, $path);
    }

    /**
     * @internal
     */
    final public static function directory(AdapterWrapper $adapter, string $path): Directory
    {
        return new Directory($adapter, $path);
    }

    final public function path(): string
    {
        return $this->path;
    }

    /**
     * @throws UnsupportedFeature If adapter does not support accessing url
     */
    final public function url(): Url
    {
        return $this->adapter->url($this->path);
    }

    final public function isDirectory(): bool
    {
        return $this instanceof Directory;
    }

    final public function isFile(): bool
    {
        return $this instanceof File;
    }

    final public function supports(string $feature): bool
    {
        return $this->adapter->supports($feature);
    }

    abstract public function real(): \SplFileInfo;
}
