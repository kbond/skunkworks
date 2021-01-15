<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface CreateDirectory
{
    /**
     * If $path already exists, do nothing.
     */
    public function mkdir(string $path): void;
}
