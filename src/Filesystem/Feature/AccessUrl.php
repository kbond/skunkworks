<?php

namespace Zenstruck\Filesystem\Feature;

use Zenstruck\Uri;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface AccessUrl
{
    public function url(string $path): Uri;
}
