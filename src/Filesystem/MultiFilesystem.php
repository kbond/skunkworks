<?php

namespace Zenstruck\Filesystem;

use Psr\Container\ContainerInterface;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Node\Directory;
use Zenstruck\Filesystem\Node\File;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MultiFilesystem implements Filesystem
{
    private $filesystems;
    private ?string $default;

    /**
     * @param array|ContainerInterface $filesystems
     */
    public function __construct($filesystems, ?string $default = null)
    {
        if (!\is_array($filesystems) && !$filesystems instanceof ContainerInterface) {
            throw new \InvalidArgumentException('$filesystems must be an array or a container.');
        }

        $this->filesystems = $filesystems;
        $this->default = $default;
    }

    public function get(?string $name = null): Filesystem
    {
        $name = $name ?? $this->default;

        if (null === $name) {
            throw new \LogicException('Default filesystem name not set.');
        }

        if (\is_array($this->filesystems) && \array_key_exists($name, $this->filesystems)) {
            return $this->filesystems[$name];
        }

        if ($this->filesystems instanceof ContainerInterface && $this->filesystems->has($name)) {
            return $this->filesystems->get($name);
        }

        throw new \InvalidArgumentException("Filesystem \"{$name}\" not found.");
    }

    public function node(string $path = ''): Node
    {
        [$filesystem, $path] = $this->parsePath($path);

        return $filesystem->node($path);
    }

    public function file(string $path): File
    {
        [$filesystem, $path] = $this->parsePath($path);

        return $filesystem->file($path);
    }

    public function directory(string $path = ''): Directory
    {
        [$filesystem, $path] = $this->parsePath($path);

        return $filesystem->directory($path);
    }

    public function exists(string $path = ''): bool
    {
        [$filesystem, $path] = $this->parsePath($path);

        return $filesystem->exists($path);
    }

    public function copy(string $source, string $destination): void
    {
        [$sourceFilesystem, $sourcePath] = $this->parsePath($source);
        [$destFilesystem, $destPath] = $this->parsePath($destination);

        if ($sourceFilesystem === $destFilesystem) {
            // same filesystem
            $sourceFilesystem->copy($sourcePath, $destPath);

            return;
        }

        /** @var Node $sourceFile */
        $sourceFile = $sourceFilesystem->node($sourcePath);

        if ($sourceFile->isDirectory()) {
            throw new \LogicException('Cannot copy directories across filesystems.');
        }

        $destFilesystem->write($destPath, $sourceFile);
    }

    public function move(string $source, string $destination): void
    {
        [$sourceFilesystem, $sourcePath] = $this->parsePath($source);
        [$destFilesystem, $destPath] = $this->parsePath($destination);

        if ($sourceFilesystem === $destFilesystem) {
            // same filesystem
            $sourceFilesystem->move($sourcePath, $destPath);

            return;
        }

        /** @var Node $sourceFile */
        $sourceFile = $sourceFilesystem->node($sourcePath);

        if ($sourceFile->isDirectory()) {
            throw new \LogicException('Cannot move directories across filesystems.');
        }

        $destFilesystem->write($destPath, $sourceFile);
        $sourceFilesystem->delete($sourcePath);
    }

    public function delete(string $path = ''): void
    {
        [$filesystem, $path] = $this->parsePath($path);

        $filesystem->delete($path);
    }

    public function mkdir(string $path = ''): void
    {
        [$filesystem, $path] = $this->parsePath($path);

        $filesystem->mkdir($path);
    }

    public function write(string $path, $value): void
    {
        [$filesystem, $path] = $this->parsePath($path);

        $filesystem->write($path, $value);
    }

    public function supports(string $feature, ?string $name = null): bool
    {
        return $this->get($name)->supports($feature);
    }

    private function parsePath(string $path): array
    {
        $parts = \explode('://', $path, 2);

        if (2 !== \count($parts)) {
            return [$this->get(), $path];
        }

        return [$this->get($parts[0]), $parts[1]];
    }
}
