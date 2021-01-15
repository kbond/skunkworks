<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface CopyDirectory
{
    public function copyDirectory(string $source, string $destination): void;
}
