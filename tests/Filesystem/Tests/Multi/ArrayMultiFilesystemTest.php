<?php

namespace Zenstruck\Filesystem\Tests\Multi;

use Zenstruck\Filesystem\MultiFilesystem;
use Zenstruck\Filesystem\Tests\MultiFilesystemTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ArrayMultiFilesystemTest extends MultiFilesystemTest
{
    protected function createForArray(array $filesystems): MultiFilesystem
    {
        return new MultiFilesystem($filesystems);
    }
}
