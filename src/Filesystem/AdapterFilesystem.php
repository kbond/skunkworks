<?php

namespace Zenstruck\Filesystem;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Adapter\AdapterWrapper;
use Zenstruck\Filesystem\Exception\NodeTypeMismatch;
use Zenstruck\Filesystem\Exception\NotFound;
use Zenstruck\Filesystem\Exception\PathOutsideRoot;
use Zenstruck\Filesystem\Exception\RuntimeException;
use Zenstruck\Filesystem\Exception\UnsupportedFeature;
use Zenstruck\Filesystem\Node\Directory;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Url\Exception\PathOutsideRoot as UrlPathOutsideRoot;
use Zenstruck\Url\Path;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AdapterFilesystem implements Filesystem
{
    private AdapterWrapper $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = AdapterWrapper::wrap($adapter);
    }

    /**
     * @throws NotFound        If $path is not found
     * @throws PathOutsideRoot If $path is outside of the filesystem's root
     */
    public function node(string $path = ''): Node
    {
        $path = self::normalizePath($path);

        if (Adapter::TYPE_FILE === $this->adapter->type($path)) {
            return Node::file($this->adapter, $path);
        }

        return Node::directory($this->adapter, $path);
    }

    /**
     * @throws NotFound         If $path is not found
     * @throws PathOutsideRoot  If $path is outside of the filesystem's root
     * @throws NodeTypeMismatch If $path is not a file
     */
    public function file(string $path): File
    {
        $file = $this->node(self::normalizePath($path));

        if (!$file instanceof File) {
            throw new NodeTypeMismatch("{$path} is not a file.");
        }

        return $file;
    }

    /**
     * @throws NotFound         If $path is not found
     * @throws PathOutsideRoot  If $path is outside of the filesystem's root
     * @throws NodeTypeMismatch If $path is not a directory
     */
    public function directory(string $path = ''): Directory
    {
        $directory = $this->node(self::normalizePath($path));

        if (!$directory instanceof Directory) {
            throw new NodeTypeMismatch("{$path} is not a directory.");
        }

        return $directory;
    }

    /**
     * @throws PathOutsideRoot If $path is outside of the filesystem's root
     */
    public function exists(string $path = ''): bool
    {
        try {
            $this->adapter->type(self::normalizePath($path));
        } catch (NotFound $e) {
            return false;
        }

        return true;
    }

    /**
     * Copy a file or directory. Always overwrites if exists.
     *
     * @throws UnsupportedFeature If adapter does not support writing files/directories
     * @throws PathOutsideRoot    If $source or $destination is outside of the filesystem's root
     * @throws RuntimeException   If the operation failed
     */
    public function copy(string $source, string $destination): void
    {
        $source = self::normalizePath($source);
        $destination = self::normalizePath($destination);

        try {
            if ($this->node($source)->isFile()) {
                $this->adapter->copyFile($source, $destination);

                return;
            }

            $this->adapter->copyDirectory($source, $destination);
        } catch (UnsupportedFeature $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw RuntimeException::wrap($e, 'Unable to copy "%s" to "%s".', $source, $destination);
        }
    }

    /**
     * Move a file or directory. Always overwrites if exists.
     *
     * @throws UnsupportedFeature If adapter does not support deleting files/directories
     * @throws PathOutsideRoot    If $source or $destination is outside of the filesystem's root
     * @throws RuntimeException   If the operation failed
     */
    public function move(string $source, string $destination): void
    {
        $source = self::normalizePath($source);
        $destination = self::normalizePath($destination);

        try {
            if ($this->node($source)->isFile()) {
                $this->adapter->moveFile($source, $destination);

                return;
            }

            $this->adapter->moveDirectory($source, $destination);
        } catch (UnsupportedFeature $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw RuntimeException::wrap($e, 'Unable to move "%s" to "%s".', $source, $destination);
        }
    }

    /**
     * Remove the file/directory at $path. If does not exist, do nothing.
     *
     * @throws UnsupportedFeature If adapter does not support deleting files/directories
     * @throws PathOutsideRoot    If $path is outside of the filesystem's root
     * @throws RuntimeException   If the operation failed
     */
    public function delete(string $path = ''): void
    {
        $path = self::normalizePath($path);

        try {
            if ($this->node($path)->isFile()) {
                $this->adapter->deleteFile($path);

                return;
            }

            $this->adapter->deleteDirectory($path);
        } catch (NotFound $e) {
            return;
        } catch (UnsupportedFeature $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw RuntimeException::wrap($e, 'Unable to remove "%s".', $path);
        }
    }

    /**
     * Make a directory at $path. If already exists, do nothing.
     *
     * @throws UnsupportedFeature If adapter does not support writing directories
     * @throws PathOutsideRoot    If $path is outside of the filesystem's root
     * @throws RuntimeException   If the operation failed
     */
    public function mkdir(string $path = ''): void
    {
        try {
            $this->adapter->mkdir(self::normalizePath($path));
        } catch (UnsupportedFeature $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw RuntimeException::wrap($e, 'Unable to make directory at "%s".', $path);
        }
    }

    /**
     * Write to a file with an existing file, string contents or resource.
     * Always overwrites if exists.
     *
     * @param resource|string|\SplFileInfo|File $value string: file contents or filename
     *
     * @throws UnsupportedFeature If adapter does not support writing files
     * @throws PathOutsideRoot    If $path is outside of the filesystem's root
     * @throws RuntimeException   If the operation failed
     */
    public function write(string $path, $value): void
    {
        if (\is_string($value)) {
            try {
                if ((new SymfonyFilesystem())->exists($value)) {
                    $value = new \SplFileInfo($value);
                }
            } catch (IOException $e) {
                // value length was too long to be a filename, keep as string
            }
        }

        $closeResource = false;

        if ($value instanceof File) {
            $value = $value->read();
            $closeResource = true;
        }

        if (!\is_string($value) && !\is_resource($value) && !$value instanceof \SplFileInfo) {
            throw new \InvalidArgumentException(\sprintf('"%s" is an invalid $value.', get_debug_type($value)));
        }

        $path = self::normalizePath($path);

        try {
            $this->adapter->write($path, $value);
        } catch (UnsupportedFeature $e) {
            throw $e;
        } catch (\Throwable $e) {
            $type = $value instanceof \SplFileInfo ? $value : get_debug_type($value);

            throw RuntimeException::wrap($e, 'Unable to write "%s" to "%s".', $type, $path);
        }

        if ($closeResource && \is_resource($value)) {
            \fclose($value);
        }
    }

    public function supports(string $feature): bool
    {
        return $this->adapter->supports($feature);
    }

    private static function normalizePath(string $path): string
    {
        try {
            return (new Path($path))->absolute();
        } catch (UrlPathOutsideRoot $e) {
            throw new PathOutsideRoot($e->getMessage(), $e);
        }
    }
}
