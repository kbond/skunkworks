<?php

namespace Zenstruck\Filesystem\Adapter;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Exception\NotFound;
use Zenstruck\Filesystem\Feature\CreateDirectory;
use Zenstruck\Filesystem\Feature\DeleteDirectory;
use Zenstruck\Filesystem\Feature\DeleteFile;
use Zenstruck\Filesystem\Feature\ReadDirectory;
use Zenstruck\Filesystem\Feature\WriteFile;
use Zenstruck\Filesystem\Util\ResourceWrapper;
use Zenstruck\Uri\Path;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ZipArchiveAdapter implements Adapter, DeleteDirectory, DeleteFile, ReadDirectory, CreateDirectory, WriteFile
{
    private string $file;
    private ?\ZipArchive $archive = null;
    private bool $autoclose = true;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function open(): void
    {
        $this->autoclose = false;
    }

    public function save(?callable $callback = null): void
    {
        $this->autoclose = true;

        $this->close($callback);
    }

    public function type(string $path): string
    {
        if (null === $node = $this->node($path)) {
            throw NotFound::forPath($path);
        }

        return $node;
    }

    public function read(string $path)
    {
        if (false === $resource = $this->archive()->getStream(self::normalizePath($path))) {
            throw new \RuntimeException("Error reading file \"{$path}\".");
        }

        return $resource;
    }

    public function contents(string $path): string
    {
        if (false === $contents = $this->archive()->getFromName(self::normalizePath($path))) {
            throw new \RuntimeException("Error accessing file contents for \"{$path}\".");
        }

        return $contents;
    }

    public function modifiedAt(string $path): int
    {
        if (null === $modified = $this->stats($path)['mtime'] ?? null) {
            throw new \RuntimeException("Error accessing file last modified for \"{$path}\".");
        }

        return $modified;
    }

    public function mimeType(string $path): string
    {
        throw new \LogicException('Cannot access mime type.');
    }

    public function size(string $path): int
    {
        if (null === $size = $this->stats($path)['size'] ?? null) {
            throw new \RuntimeException("Error accessing file size for \"{$path}\".");
        }

        return $size;
    }

    public function write(string $path, $value): void
    {
        if (self::TYPE_DIRECTORY === $this->node($path)) {
            throw new \RuntimeException('Cannot write to directory.');
        }

        if ($value instanceof \SplFileInfo) {
            if (false === $this->archive()->addFile($value, self::normalizePath($path))) {
                throw new \RuntimeException('Error writing file.');
            }

            $this->close();

            return;
        }

        if (\is_resource($value)) {
            $resource = ResourceWrapper::wrap($value);
            $value = $resource->contents();
            $resource->close();
        }

        if (false === $this->archive()->addFromString(self::normalizePath($path), $value)) {
            throw new \RuntimeException('Error writing file.');
        }

        $this->close();
    }

    public function mkdir(string $path): void
    {
        switch ($this->node($path)) {
            case self::TYPE_DIRECTORY:
                return;
            case self::TYPE_FILE:
                throw new \RuntimeException("\"{$path}\" is a file.");
        }

        if (false === $this->archive()->addEmptyDir(self::normalizePath($path))) {
            throw new \RuntimeException('Unable to create directory.');
        }

        $this->close();
    }

    public function deleteDirectory(string $path): void
    {
        $normalized = self::normalizePath($path);

        foreach ($this->all() as $i => $name) {
            if (!empty($normalized) && 0 !== \mb_strpos($name, $normalized)) {
                continue;
            }

            if (false === $this->archive()->deleteIndex($i)) {
                throw new \RuntimeException("Unable to delete \"{$name}\".");
            }
        }

        $this->close();

        if (empty($normalized)) {
            // deleting root deletes the file
            (new SymfonyFilesystem())->remove($this->file);
        }
    }

    public function deleteFile(string $path): void
    {
        if (false === $this->archive()->deleteName(self::normalizePath($path))) {
            throw new \RuntimeException("Unable to delete \"{$path}\".");
        }
    }

    public function listing(string $path): iterable
    {
        $normalized = self::normalizePath($path);
        $depth = empty($normalized) ? 1 : \count(\explode('/', $normalized)) + 1;
        $ret = [];

        foreach ($this->all() as $name) {
            if ($depth > 1 && 0 !== \mb_strpos($name, $normalized)) {
                continue;
            }

            if (\count($parts = \explode('/', $name)) > $depth) {
                $dir = \implode('/', \array_slice($parts, 0, $depth));

                $ret["/{$dir}"] = self::TYPE_DIRECTORY;

                continue;
            }

            if ('/' === \mb_substr($name, -1)) {
                continue;
            }

            $ret["/{$name}"] = self::TYPE_FILE;
        }

        return $ret;
    }

    private function node(string $path): ?string
    {
        if (!\file_exists($this->file)) {
            return null;
        }

        if ('/' === $path) {
            return self::TYPE_DIRECTORY;
        }

        $normalized = self::normalizePath($path);

        if (false !== $this->archive()->locateName($normalized)) {
            return self::TYPE_FILE;
        }

        // empty directories can be located via name suffixed with "/"
        if (false !== $this->archive()->locateName($normalized.'/')) { // check for empty dir
            return self::TYPE_DIRECTORY;
        }

        // non-empty directories can only be found by iterating all files and finding a file in that directory
        foreach ($this->all() as $name) {
            if (0 === \mb_strpos($name, $normalized)) {
                return self::TYPE_DIRECTORY;
            }
        }

        return null;
    }

    private function all(): iterable
    {
        for ($i = 0; $i < $this->archive()->numFiles; ++$i) {
            if (false === $stats = $this->archive()->statIndex($i)) {
                continue;
            }

            yield $i => $stats['name'];
        }
    }

    private static function normalizePath(string $path): string
    {
        return (new Path($path))->trim();
    }

    private function stats(string $path): array
    {
        if (false === $stats = $this->archive()->statName(self::normalizePath($path))) {
            throw new \RuntimeException("Error accessing metadata for \"{$path}\".");
        }

        return $stats;
    }

    private function close(?callable $callback = null): void
    {
        if (!$this->autoclose || !$this->archive) {
            return;
        }

        if ($callback && \method_exists($this->archive, 'registerProgressCallback')) {
            $this->archive->registerProgressCallback(0.01, $callback);
        }

        $this->archive->close();
        $this->archive = null;
    }

    private function archive(): \ZipArchive
    {
        if ($this->archive) {
            return $this->archive;
        }

        $this->archive = new \ZipArchive();

        if (!\file_exists($dir = \dirname($this->file))) {
            (new SymfonyFilesystem())->mkdir($dir);
        }

        if (true !== $this->archive->open($this->file, \ZipArchive::CREATE)) {
            // todo improve error based on return constants (https://www.php.net/manual/en/ziparchive.open.php#refsect1-ziparchive.open-returnvalues)
            throw new \RuntimeException("Could not open \"{$this->file}\" as archive.");
        }

        return $this->archive;
    }
}
