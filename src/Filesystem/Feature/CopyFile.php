<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface CopyFile
{
    public function copyFile(string $source, string $destination): void;
}
