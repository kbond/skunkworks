<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface MoveDirectory
{
    public function moveDirectory(string $source, string $destination): void;
}
