<?php

namespace Zenstruck\Filesystem\Adapter;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Feature\CopyDirectory;
use Zenstruck\Filesystem\Feature\CopyFile;
use Zenstruck\Filesystem\Feature\CreateDirectory;
use Zenstruck\Filesystem\Feature\DeleteDirectory;
use Zenstruck\Filesystem\Feature\DeleteFile;
use Zenstruck\Filesystem\Feature\MoveDirectory;
use Zenstruck\Filesystem\Feature\MoveFile;
use Zenstruck\Filesystem\Feature\ReadDirectory;
use Zenstruck\Filesystem\Feature\WriteFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class StaticInMemoryAdapter implements Adapter, DeleteDirectory, MoveDirectory, DeleteFile, MoveFile, ReadDirectory, CreateDirectory, CopyDirectory, CopyFile, WriteFile
{
    /** @var InMemoryAdapter[] */
    private static array $adapters = [];
    private string $name;

    public function __construct(string $name = 'default')
    {
        $this->name = $name;
    }

    public static function reset(): void
    {
        self::$adapters = [];
    }

    public function type(string $path): string
    {
        return $this->adapter()->type($path);
    }

    public function read(string $path)
    {
        return $this->adapter()->read($path);
    }

    public function contents(string $path): string
    {
        return $this->adapter()->contents($path);
    }

    public function modifiedAt(string $path): int
    {
        return $this->adapter()->modifiedAt($path);
    }

    public function mimeType(string $path): string
    {
        return $this->adapter()->mimeType($path);
    }

    public function size(string $path): int
    {
        return $this->adapter()->size($path);
    }

    public function copyDirectory(string $source, string $destination): void
    {
        $this->adapter()->copyDirectory($source, $destination);
    }

    public function copyFile(string $source, string $destination): void
    {
        $this->adapter()->copyFile($source, $destination);
    }

    public function mkdir(string $path): void
    {
        $this->adapter()->mkdir($path);
    }

    public function deleteDirectory(string $path): void
    {
        $this->adapter()->deleteDirectory($path);
    }

    public function deleteFile(string $path): void
    {
        $this->adapter()->deleteFile($path);
    }

    public function moveDirectory(string $source, string $destination): void
    {
        $this->adapter()->moveDirectory($source, $destination);
    }

    public function moveFile(string $source, string $destination): void
    {
        $this->adapter()->moveFile($source, $destination);
    }

    public function listing(string $path): iterable
    {
        return $this->adapter()->listing($path);
    }

    public function write(string $path, $value): void
    {
        $this->adapter()->write($path, $value);
    }

    private function adapter(): InMemoryAdapter
    {
        return self::$adapters[$this->name] ??= new InMemoryAdapter();
    }
}
