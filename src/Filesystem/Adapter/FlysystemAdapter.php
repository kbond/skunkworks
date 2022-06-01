<?php

namespace Zenstruck\Filesystem\Adapter;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Exception\NotFound;
use Zenstruck\Filesystem\Feature\CopyFile;
use Zenstruck\Filesystem\Feature\CreateDirectory;
use Zenstruck\Filesystem\Feature\DeleteDirectory;
use Zenstruck\Filesystem\Feature\DeleteFile;
use Zenstruck\Filesystem\Feature\MoveFile;
use Zenstruck\Filesystem\Feature\ReadDirectory;
use Zenstruck\Filesystem\Feature\WriteFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FlysystemAdapter implements Adapter, DeleteDirectory, DeleteFile, MoveFile, ReadDirectory, CreateDirectory, CopyFile, WriteFile
{
    private FilesystemOperator $flysystem;

    public function __construct(FilesystemOperator $flysystem)
    {
        $this->flysystem = $flysystem;
    }

    public function type(string $path): string
    {
        if ($this->flysystem->fileExists($path)) {
            return self::TYPE_FILE;
        }

        if ($this->directoryExists($path)) {
            return self::TYPE_DIRECTORY;
        }

        throw NotFound::forPath($path);
    }

    public function read(string $path)
    {
        return $this->flysystem->readStream($path);
    }

    public function contents(string $path): string
    {
        return $this->flysystem->read($path);
    }

    public function modifiedAt(string $path): int
    {
        return $this->flysystem->lastModified($path);
    }

    public function mimeType(string $path): string
    {
        return $this->flysystem->mimeType($path);
    }

    public function size(string $path): int
    {
        return $this->flysystem->fileSize($path);
    }

    public function copyFile(string $source, string $destination): void
    {
        $this->flysystem->copy($source, $destination);
    }

    public function mkdir(string $path): void
    {
        $this->flysystem->createDirectory($path);
    }

    public function deleteDirectory(string $path): void
    {
        $this->flysystem->deleteDirectory($path);
    }

    public function deleteFile(string $path): void
    {
        $this->flysystem->delete($path);
    }

    public function moveFile(string $source, string $destination): void
    {
        $this->copyFile($source, $destination);
        $this->deleteFile($source);
    }

    public function listing(string $path): iterable
    {
        foreach ($this->flysystem->listContents($path) as $node) {
            /* @var StorageAttributes $node */
            yield "/{$node->path()}" => StorageAttributes::TYPE_FILE === $node->type() ? self::TYPE_FILE : self::TYPE_DIRECTORY;
        }
    }

    public function write(string $path, $value): void
    {
        if ($value instanceof \SplFileInfo) {
            $value = \file_get_contents($value);
        }

        if (\is_string($value)) {
            $this->flysystem->write($path, $value);

            return;
        }

        $this->flysystem->writeStream($path, $value);
    }

    private function directoryExists(string $path): bool
    {
        if (\method_exists($this->flysystem, 'directoryExists')) {
            return $this->flysystem->directoryExists($path);
        }

        try {
            // I believe this is the only way to check if path is a directory in V2 (exception thrown means does not exist)
            $this->flysystem->visibility($path);

            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }
}
