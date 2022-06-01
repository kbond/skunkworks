<?php

namespace Zenstruck\Filesystem;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Adapter\ZipArchiveAdapter;
use Zenstruck\Filesystem\Node\Directory;
use Zenstruck\Filesystem\Node\File;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ArchiveFile extends \SplFileInfo implements Filesystem
{
    private Filesystem $filesystem;
    private ZipArchiveAdapter $adapter;

    public function __construct(?string $filename = null)
    {
        if (!$filename) {
            $tempFile = new TempFile();
            $tempFile->delete();

            $filename = (string) $tempFile;
        }

        parent::__construct($filename);

        $this->filesystem = new AdapterFilesystem($this->adapter = new ZipArchiveAdapter($filename));
    }

    /**
     * Subsequent write operations will be "queued".
     */
    public function beginTransaction(): void
    {
        $this->adapter->open();
    }

    /**
     * Commit the queued write operations.
     *
     * @param callable|null $callback Progress callback that takes the percentage (float between 0.0 and 1.0)
     *                                as the argument. Called a maximum of 100 times.
     */
    public function commit(?callable $callback = null): void
    {
        $this->adapter->save($callback);
    }

    public function node(string $path = ''): Node
    {
        return $this->filesystem->node($path);
    }

    public function file(string $path): File
    {
        return $this->filesystem->file($path);
    }

    public function directory(string $path = ''): Directory
    {
        return $this->filesystem->directory($path);
    }

    public function exists(string $path = ''): bool
    {
        return $this->filesystem->exists($path);
    }

    public function copy(string $source, string $destination): void
    {
        $this->filesystem->copy($source, $destination);
    }

    public function move(string $source, string $destination): void
    {
        $this->filesystem->move($source, $destination);
    }

    public function delete(string $path = ''): void
    {
        $this->filesystem->delete($path);
    }

    public function mkdir(string $path = ''): void
    {
        $this->filesystem->mkdir($path);
    }

    public function write(string $path, $value): void
    {
        $this->filesystem->write($path, $value);
    }

    public function supports(string $feature): bool
    {
        return $this->filesystem->supports($feature);
    }
}
