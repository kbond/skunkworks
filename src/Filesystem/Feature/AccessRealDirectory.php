<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface AccessRealDirectory
{
    public function realDirectory(string $path): \SplFileInfo;
}
