<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface AccessRealFile
{
    public function realFile(string $path): \SplFileInfo;
}
