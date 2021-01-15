<?php

namespace Zenstruck\Filesystem\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface WriteFile
{
    /**
     * @param string|\SplFileInfo|resource $value string: file contents to write
     */
    public function write(string $path, $value): void;
}
