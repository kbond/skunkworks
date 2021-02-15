<?php

namespace Zenstruck\Filesystem\Adapter;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Exception\UnsupportedFeature;
use Zenstruck\Filesystem\Feature\AccessRealDirectory;
use Zenstruck\Filesystem\Feature\AccessRealFile;
use Zenstruck\Filesystem\Feature\AccessUrl;
use Zenstruck\Filesystem\Feature\All;
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
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AdapterWrapper implements Adapter, All
{
    private Adapter $next;

    public function __construct(Adapter $adapter)
    {
        $this->next = $adapter;
    }

    final public static function wrap(Adapter $adapter): self
    {
        return $adapter instanceof self ? $adapter : new self($adapter);
    }

    public function type(string $path): string
    {
        return $this->next->type($path);
    }

    public function read(string $path)
    {
        return $this->next->read($path);
    }

    public function contents(string $path): string
    {
        return $this->next->contents($path);
    }

    public function modifiedAt(string $path): int
    {
        return $this->next->modifiedAt($path);
    }

    public function mimeType(string $path): string
    {
        return $this->next->mimeType($path);
    }

    public function size(string $path): int
    {
        return $this->next->size($path);
    }

    public function url(string $path): Url
    {
        return $this->ensureSupports(AccessUrl::class)->url($path);
    }

    public function moveDirectory(string $source, string $destination): void
    {
        $this->ensureSupports(MoveDirectory::class)->moveDirectory($source, $destination);
    }

    public function moveFile(string $source, string $destination): void
    {
        $this->ensureSupports(MoveFile::class)->moveFile($source, $destination);
    }

    public function deleteDirectory(string $path): void
    {
        $this->ensureSupports(DeleteDirectory::class)->deleteDirectory($path);
    }

    public function mkdir(string $path): void
    {
        $this->ensureSupports(CreateDirectory::class)->mkdir($path);
    }

    public function copyDirectory(string $source, string $destination): void
    {
        $this->ensureSupports(CopyDirectory::class)->copyDirectory($source, $destination);
    }

    public function copyFile(string $source, string $destination): void
    {
        $this->ensureSupports(CopyFile::class)->copyFile($source, $destination);
    }

    public function write(string $path, $value): void
    {
        $this->ensureSupports(WriteFile::class)->write($path, $value);
    }

    public function deleteFile(string $path): void
    {
        $this->ensureSupports(DeleteFile::class)->deleteFile($path);
    }

    public function realFile(string $path): \SplFileInfo
    {
        return $this->ensureSupports(AccessRealFile::class)->realFile($path);
    }

    public function realDirectory(string $path): \SplFileInfo
    {
        return $this->ensureSupports(AccessRealDirectory::class)->realFile($path);
    }

    public function listing(string $path): iterable
    {
        return $this->ensureSupports(ReadDirectory::class)->listing($path);
    }

    public function fileChecksum(string $path): string
    {
        return $this->ensureSupports(FileChecksum::class)->fileChecksum($path);
    }

    public function supports(string $feature): bool
    {
        return $this->next instanceof self ? $this->next->supports($feature) : $this->next instanceof $feature;
    }

    /**
     * @return Adapter the "real" adapter
     */
    private function adapter(): Adapter
    {
        return $this->next instanceof self ? $this->next->adapter() : $this->next;
    }

    /**
     * @return All|Adapter
     */
    private function ensureSupports(string $feature): Adapter
    {
        if (!$this->supports($feature)) {
            throw new UnsupportedFeature(\sprintf('The "%s" adapter does not support "%s".', \get_class($this->adapter()), $feature));
        }

        return $this->next;
    }
}
