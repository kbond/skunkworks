<?php

namespace Zenstruck\Filesystem\Feature;

use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface AccessUrl
{
    public function url(string $path): Url;
}
