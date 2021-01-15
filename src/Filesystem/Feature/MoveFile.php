<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface MoveFile
{
    public function moveFile(string $source, string $destination): void;
}
