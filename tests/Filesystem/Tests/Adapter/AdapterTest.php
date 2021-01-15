<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\Tests\FilesystemTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class AdapterTest extends FilesystemTest
{
    protected function createFilesystem(): Filesystem
    {
        return new AdapterFilesystem($this->createAdapter());
    }

    abstract protected function createAdapter(): Adapter;
}
