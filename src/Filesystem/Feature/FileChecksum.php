<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface FileChecksum
{
    public function fileChecksum(string $path): string;
}
