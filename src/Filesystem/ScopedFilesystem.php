<?php

namespace Zenstruck\Filesystem;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Node\Directory;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Uri\Path;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ScopedFilesystem implements Filesystem
{
    private Filesystem $filesystem;
    private Path $prefix;

    public function __construct(Filesystem $filesystem, string $prefix)
    {
        $this->filesystem = $filesystem;
        $this->prefix = new Path($prefix);
    }

    public function node(string $path = ''): Node
    {
        return $this->filesystem->node($this->prefix->append($path));
    }

    public function file(string $path): File
    {
        return $this->filesystem->file($this->prefix->append($path));
    }

    public function directory(string $path = ''): Directory
    {
        return $this->filesystem->directory($this->prefix->append($path));
    }

    public function exists(string $path = ''): bool
    {
        return $this->filesystem->exists($this->prefix->append($path));
    }

    public function copy(string $source, string $destination): void
    {
        $this->filesystem->copy($this->prefix->append($source), $this->prefix->append($destination));
    }

    public function move(string $source, string $destination): void
    {
        $this->filesystem->move($this->prefix->append($source), $this->prefix->append($destination));
    }

    public function delete(string $path = ''): void
    {
        $this->filesystem->delete($this->prefix->append($path));
    }

    public function mkdir(string $path = ''): void
    {
        $this->filesystem->mkdir($this->prefix->append($path));
    }

    public function write(string $path, $value): void
    {
        $this->filesystem->write($this->prefix->append($path), $value);
    }

    public function supports(string $feature): bool
    {
        return $this->filesystem->supports($feature);
    }
}
