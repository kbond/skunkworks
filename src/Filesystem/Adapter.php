<?php

namespace Zenstruck\Filesystem;

use Zenstruck\Filesystem\Exception\NotFound;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Adapter
{
    public const TYPE_FILE = 'file';
    public const TYPE_DIRECTORY = 'directory';

    /**
     * @throws NotFound If $path is not found
     */
    public function type(string $path): string;

    /**
     * @return resource
     */
    public function read(string $path);

    public function contents(string $path): string;

    public function modifiedAt(string $path): int;

    public function mimeType(string $path): string;

    public function size(string $path): int;
}
