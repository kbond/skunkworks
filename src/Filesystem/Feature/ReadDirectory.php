<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface ReadDirectory
{
    /**
     * @return iterable<string, string> path => type
     */
    public function listing(string $path): iterable;
}
