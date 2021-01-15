<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface DeleteDirectory
{
    /**
     * If path does not exist, do nothing.
     */
    public function deleteDirectory(string $path): void;
}
