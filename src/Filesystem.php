<?php

namespace Zenstruck;

use Zenstruck\Filesystem\Exception\NodeTypeMismatch;
use Zenstruck\Filesystem\Exception\NotFound;
use Zenstruck\Filesystem\Exception\PathOutsideRoot;
use Zenstruck\Filesystem\Exception\RuntimeException;
use Zenstruck\Filesystem\Exception\UnsupportedFeature;
use Zenstruck\Filesystem\Node;
use Zenstruck\Filesystem\Node\Directory;
use Zenstruck\Filesystem\Node\File;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Filesystem
{
    /**
     * @throws NotFound        If $path is not found
     * @throws PathOutsideRoot If $path is outside of the filesystem's root
     */
    public function node(string $path = ''): Node;

    /**
     * @throws NotFound         If $path is not found
     * @throws PathOutsideRoot  If $path is outside of the filesystem's root
     * @throws NodeTypeMismatch If $path is not a file
     */
    public function file(string $path): File;

    /**
     * @throws NotFound         If $path is not found
     * @throws PathOutsideRoot  If $path is outside of the filesystem's root
     * @throws NodeTypeMismatch If $path is not a directory
     */
    public function directory(string $path = ''): Directory;

    /**
     * @throws PathOutsideRoot If $path is outside of the filesystem's root
     */
    public function exists(string $path = ''): bool;

    /**
     * Copy a file or directory. Always overwrites if exists.
     *
     * @throws UnsupportedFeature If adapter does not support writing files/directories
     * @throws PathOutsideRoot    If $source or $destination is outside of the filesystem's root
     * @throws RuntimeException   If the operation failed
     */
    public function copy(string $source, string $destination): void;

    /**
     * Move a file or directory. Always overwrites if exists.
     *
     * @throws UnsupportedFeature If adapter does not support deleting files/directories
     * @throws PathOutsideRoot    If $source or $destination is outside of the filesystem's root
     * @throws RuntimeException   If the operation failed
     */
    public function move(string $source, string $destination): void;

    /**
     * Remove the file/directory at $path. If does not exist, do nothing.
     *
     * @throws UnsupportedFeature If adapter does not support deleting files/directories
     * @throws PathOutsideRoot    If $path is outside of the filesystem's root
     * @throws RuntimeException   If the operation failed
     */
    public function delete(string $path = ''): void;

    /**
     * Make a directory at $path. If already exists, do nothing.
     *
     * @throws UnsupportedFeature If adapter does not support writing directories
     * @throws PathOutsideRoot    If $path is outside of the filesystem's root
     * @throws RuntimeException   If the operation failed
     */
    public function mkdir(string $path = ''): void;

    /**
     * Write to a file with an existing file, string contents or resource.
     * Always overwrites if exists.
     *
     * @param resource|string|\SplFileInfo|Node $value string: file contents or filename
     *
     * @throws UnsupportedFeature If adapter does not support writing files
     * @throws PathOutsideRoot    If $path is outside of the filesystem's root
     * @throws RuntimeException   If the operation failed
     */
    public function write(string $path, $value): void;

    public function supports(string $feature): bool;
}
