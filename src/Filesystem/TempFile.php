<?php

namespace Zenstruck\Filesystem;

use Zenstruck\Filesystem\Util\ResourceWrapper;

/**
 * Creates a temporary file or wraps an existing file to be deleted
 * at the end of the script.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TempFile extends \SplFileInfo
{
    public function __construct(?string $filename = null)
    {
        parent::__construct($filename ?? self::tempFile());

        if ($this->isDir()) {
            throw new \LogicException("\"{$filename}\" is a directory.");
        }

        // delete on script end
        \register_shutdown_function([$this, 'delete']);
    }

    /**
     * @param resource $resource
     */
    public static function forStream($resource): self
    {
        ResourceWrapper::open($file = new self(), 'w')->write($resource)->close();

        return $file;
    }

    /**
     * @param callable(\SplFileInfo):void $callback
     */
    public static function tap(callable $callback): self
    {
        $callback($file = new self());

        return $file;
    }

    public function delete(): void
    {
        if (\file_exists($this)) {
            \unlink($this);
        }
    }

    public function getSize(): int
    {
        \clearstatcache(false, $this);

        return parent::getSize();
    }

    private static function tempFile(): string
    {
        if (false === $filename = \tempnam(\sys_get_temp_dir(), 'zsfs_')) {
            throw new \RuntimeException('Failed to create temporary file.');
        }

        return $filename;
    }
}
