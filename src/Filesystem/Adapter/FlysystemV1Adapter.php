<?php

namespace Zenstruck\Filesystem\Adapter;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
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
final class FlysystemV1Adapter implements Adapter, DeleteDirectory, DeleteFile, MoveFile, ReadDirectory, CreateDirectory, CopyFile, WriteFile
{
    private FilesystemInterface $flysystem;

    public function __construct(FilesystemInterface $flysystem)
    {
        $this->flysystem = $flysystem;
    }

    public function type(string $path): string
    {
        if ('/' === $path) {
            return self::TYPE_DIRECTORY;
        }

        try {
            $metadata = $this->flysystem->getMetadata($path);
        } catch (FileNotFoundException $e) {
            throw NotFound::forPath($path, $e);
        }

        return 'file' === $metadata['type'] ? self::TYPE_FILE : self::TYPE_DIRECTORY;
    }

    public function read(string $path)
    {
        if (false === $ret = $this->flysystem->readStream($path)) {
            throw new \RuntimeException("Unable to read \"{$path}\".");
        }

        return $ret;
    }

    public function contents(string $path): string
    {
        if (false === $ret = $this->flysystem->read($path)) {
            throw new \RuntimeException("Unable to get contents of \"{$path}\".");
        }

        return $ret;
    }

    public function modifiedAt(string $path): int
    {
        if (false === $ret = $this->flysystem->getTimestamp($path)) {
            throw new \RuntimeException("Unable to get last modified for \"{$path}\".");
        }

        return $ret;
    }

    public function mimeType(string $path): string
    {
        if (false === $ret = $this->flysystem->getMimetype($path)) {
            throw new \RuntimeException("Unable to get mime type for \"{$path}\".");
        }

        return $ret;
    }

    public function size(string $path): int
    {
        if (false === $ret = $this->flysystem->getSize($path)) {
            throw new \RuntimeException("Unable to get size of \"{$path}\".");
        }

        return $ret;
    }

    public function copyFile(string $source, string $destination): void
    {
        try {
            if (self::TYPE_FILE === $this->type($destination)) {
                $this->deleteFile($destination);
            }
        } catch (NotFound $e) {
        }

        if (false === $this->flysystem->copy($source, $destination)) {
            throw new \RuntimeException("Unable to copy \"{$source}\" to \"{$destination}\".");
        }
    }

    public function mkdir(string $path): void
    {
        if (false === $this->flysystem->createDir($path)) {
            throw new \RuntimeException("Unable to mkdir \"{$path}\".");
        }
    }

    public function deleteDirectory(string $path): void
    {
        if (false === $this->flysystem->deleteDir($path)) {
            throw new \RuntimeException("Unable to remove directory \"{$path}\".");
        }
    }

    public function deleteFile(string $path): void
    {
        if (false === $this->flysystem->delete($path)) {
            throw new \RuntimeException("Unable to remove file \"{$path}\".");
        }
    }

    public function moveFile(string $source, string $destination): void
    {
        $this->copyFile($source, $destination);
        $this->deleteFile($source);
    }

    public function listing(string $path): iterable
    {
        foreach ($this->flysystem->listContents($path) as $meta) {
            yield "/{$meta['path']}" => 'file' === $meta['type'] ? self::TYPE_FILE : self::TYPE_DIRECTORY;
        }
    }

    public function write(string $path, $value): void
    {
        if ($value instanceof \SplFileInfo) {
            $value = \file_get_contents($value);
        }

        if (\is_string($value)) {
            $this->flysystem->put($path, $value);

            return;
        }

        $this->flysystem->putStream($path, $value);
    }
}
