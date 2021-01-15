<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface DeleteFile
{
    /**
     * If $path does not exist, do nothing.
     */
    public function deleteFile(string $path): void;
}
