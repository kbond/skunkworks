<?php

namespace Zenstruck\Filesystem\Adapter;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Mime\MimeTypes;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Exception\NotFound;
use Zenstruck\Filesystem\Feature\AccessRealDirectory;
use Zenstruck\Filesystem\Feature\AccessRealFile;
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
use Zenstruck\Filesystem\TempFile;
use Zenstruck\Filesystem\Util\ResourceWrapper;
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class StreamAdapter implements Adapter, AccessRealFile, AccessRealDirectory, DeleteDirectory, MoveDirectory, DeleteFile, MoveFile, ReadDirectory, CreateDirectory, CopyDirectory, CopyFile, WriteFile, FileChecksum
{
    private static SymfonyFilesystem $fs;
    private Url $root;

    /**
     * @param Url|string $root
     */
    public function __construct($root)
    {
        $this->root = Url::new($root);
    }

    public function realFile(string $path): \SplFileInfo
    {
        if (!\file_exists($file = $this->splFile($path))) {
            throw NotFound::forPath($path);
        }

        return $file;
    }

    public function realDirectory(string $path): \SplFileInfo
    {
        return $this->realFile($path);
    }

    public function type(string $path): string
    {
        return $this->realFile($path)->isDir() ? self::TYPE_DIRECTORY : self::TYPE_FILE;
    }

    public function read(string $path)
    {
        return ResourceWrapper::open($this->realFile($path), 'r')->get();
    }

    public function contents(string $path): string
    {
        if (false === $contents = \file_get_contents($file = $this->realFile($path))) {
            throw new \RuntimeException("Unable to get contents of \"{$file}\".");
        }

        return $contents;
    }

    public function modifiedAt(string $path): int
    {
        return $this->realFile($path)->getMTime();
    }

    public function mimeType(string $path): string
    {
        if (null === $mimeType = MimeTypes::getDefault()->guessMimeType($file = $this->realFile($path))) {
            throw new \RuntimeException("Unable to get mime type for \"{$file}\".");
        }

        return $mimeType;
    }

    public function size(string $path): int
    {
        return $this->realFile($path)->getSize();
    }

    public function moveDirectory(string $source, string $destination): void
    {
        $this->moveFile($source, $destination);
    }

    public function deleteDirectory(string $path): void
    {
        $this->deleteFile($path);
    }

    public function moveFile(string $source, string $destination): void
    {
        self::fs()->rename($this->realFile($source), $this->splFile($destination), true);
    }

    public function deleteFile(string $path): void
    {
        self::fs()->remove($this->splFile($path));
    }

    public function mkdir(string $path): void
    {
        self::fs()->mkdir($this->splFile($path));
    }

    public function copyDirectory(string $source, string $destination): void
    {
        self::fs()->mirror($this->realFile($source), $this->splFile($destination), null, [
            'override' => true,
            'delete' => true,
        ]);
    }

    public function copyFile(string $source, string $destination): void
    {
        self::fs()->copy($this->realFile($source), $this->splFile($destination), true);
    }

    public function write(string $path, $value): void
    {
        $file = $this->splFile($path);

        if (\is_string($value) || \is_resource($value)) {
            self::fs()->dumpFile($file, $value);

            return;
        }

        if ($value instanceof TempFile) {
            self::fs()->rename($value, $file, true);

            return;
        }

        self::fs()->copy($value, $file, true);
    }

    public function listing(string $path): iterable
    {
        foreach (Finder::create()->in((string) $this->realFile($path))->depth(0) as $file) {
            yield (new Url\Path($path))->append($file->getFilename()) => $file->isDir() ? Adapter::TYPE_DIRECTORY : Adapter::TYPE_FILE;
        }
    }

    public function fileChecksum(string $path): string
    {
        return \md5_file($this->realFile($path), false);
    }

    private static function fs(): SymfonyFilesystem
    {
        return self::$fs ??= new SymfonyFilesystem();
    }

    private function splFile(string $path): \SplFileInfo
    {
        return new \SplFileInfo($this->root->appendPath($path));
    }
}
