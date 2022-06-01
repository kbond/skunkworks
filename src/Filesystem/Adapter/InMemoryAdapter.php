<?php

namespace Zenstruck\Filesystem\Adapter;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Exception\NotFound;
use Zenstruck\Filesystem\Feature\CopyDirectory;
use Zenstruck\Filesystem\Feature\CopyFile;
use Zenstruck\Filesystem\Feature\CreateDirectory;
use Zenstruck\Filesystem\Feature\DeleteDirectory;
use Zenstruck\Filesystem\Feature\DeleteFile;
use Zenstruck\Filesystem\Feature\FileChecksum;
use Zenstruck\Filesystem\Feature\MoveDirectory;
use Zenstruck\Filesystem\Feature\MoveFile;
use Zenstruck\Filesystem\Feature\ReadDirectory;
use Zenstruck\Filesystem\Feature\WriteFile;
use Zenstruck\Filesystem\Util\ResourceWrapper;
use Zenstruck\Uri\Path;
use Zenstruck\Utilities\ArrayAccessor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class InMemoryAdapter implements Adapter, DeleteDirectory, MoveDirectory, DeleteFile, MoveFile, ReadDirectory, CreateDirectory, CopyDirectory, CopyFile, WriteFile, FileChecksum
{
    private const ROOT = 'root';

    private ?ArrayAccessor $data = null;
    private array $modifiedAtCache = [];

    public function type(string $path): string
    {
        return \is_string($this->value($path)) ? self::TYPE_FILE : self::TYPE_DIRECTORY;
    }

    public function read(string $path)
    {
        return ResourceWrapper::inMemory()->write($this->file($path))->rewind()->get();
    }

    public function contents(string $path): string
    {
        return $this->file($path);
    }

    public function modifiedAt(string $path): int
    {
        if (null === $timestamp = $this->modifiedAtCache[$this->normalizePath($path)] ?? null) {
            throw new \RuntimeException("Unable to access modified at for \"{$path}\".");
        }

        return $timestamp;
    }

    public function mimeType(string $path): string
    {
        if (!\class_exists(\finfo::class)) {
            throw new \LogicException('fileinfo extension not available.');
        }

        return (new \finfo(\FILEINFO_MIME_TYPE))->buffer($this->file($path));
    }

    public function size(string $path): int
    {
        return \mb_strlen($this->file($path));
    }

    public function copyDirectory(string $source, string $destination): void
    {
        if ($this->isFile($destination)) {
            throw new \RuntimeException("\"{$destination}\" is a file.");
        }

        $this->data()->set($this->normalizePath($destination), $this->directory($source));
    }

    public function copyFile(string $source, string $destination): void
    {
        if ($this->isDirectory($destination)) {
            throw new \RuntimeException("\"{$destination}\" is a directory.");
        }

        $this->data()->set($this->normalizePath($destination), $this->file($source));
    }

    public function mkdir(string $path): void
    {
        if ($this->isFile($path)) {
            throw new \RuntimeException("\"{$path}\" is a file.");
        }

        if ($this->isDirectory($path)) {
            return;
        }

        $this->data()->set($this->normalizePath($path), []);
    }

    public function deleteDirectory(string $path): void
    {
        if ($this->isFile($path)) {
            throw new \RuntimeException("\"{$path}\" is a file.");
        }

        $this->data()->unset($this->normalizePath($path));
    }

    public function deleteFile(string $path): void
    {
        if ($this->isDirectory($path)) {
            throw new \RuntimeException("\"{$path}\" is a directory.");
        }

        $this->data()->unset($path = $this->normalizePath($path));
        unset($this->modifiedAtCache[$path]);
    }

    public function moveDirectory(string $source, string $destination): void
    {
        $this->copyDirectory($source, $destination);
        $this->deleteDirectory($source);
    }

    public function moveFile(string $source, string $destination): void
    {
        $this->copyFile($source, $destination);
        $this->deleteFile($source);
    }

    public function listing(string $path): iterable
    {
        foreach ($this->directory($path) as $key => $value) {
            yield (new Path($key))->prepend($path) => \is_array($value) ? self::TYPE_DIRECTORY : self::TYPE_FILE;
        }
    }

    public function write(string $path, $value): void
    {
        if ($this->isDirectory($path)) {
            throw new \RuntimeException("Cannot write file to directory \"{$path}\".");
        }

        if (\is_resource($value)) {
            $value = ResourceWrapper::wrap($value)->contents();
        }

        if ($value instanceof \SplFileInfo) {
            $value = \file_get_contents($value);
        }

        $this->data()->set($path = $this->normalizePath($path), $value);
        $this->modifiedAtCache[$path] = \time();
    }

    public function fileChecksum(string $path): string
    {
        return \md5($this->file($path), false);
    }

    private function normalizePath(string $path): string
    {
        if ('/' === $path) {
            return self::ROOT;
        }

        return self::ROOT.$path;
    }

    private function isDirectory(string $path): bool
    {
        try {
            return \is_array($this->value($path));
        } catch (NotFound $e) {
            return false;
        }
    }

    private function isFile(string $path): bool
    {
        try {
            return \is_string($this->value($path));
        } catch (NotFound $e) {
            return false;
        }
    }

    /**
     * @return string|array
     */
    private function value(string $path)
    {
        return $this->data()->get($this->normalizePath($path), NotFound::forPath($path));
    }

    private function file(string $path): string
    {
        if (!\is_string($contents = $this->value($path))) {
            throw new \RuntimeException("\"{$path}\" is not a file.");
        }

        return $contents;
    }

    private function directory(string $path): array
    {
        if (!\is_array($dir = $this->value($path))) {
            throw new \RuntimeException("\"{$path}\" is not a directory.");
        }

        return $dir;
    }

    private function data(): ArrayAccessor
    {
        return $this->data ??= new ArrayAccessor([], '/');
    }
}
