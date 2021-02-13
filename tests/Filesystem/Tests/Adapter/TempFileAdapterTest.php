<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\FilesystemFactory;
use Zenstruck\Filesystem\Tests\Feature\AccessRealFileTests;
use Zenstruck\Filesystem\Tests\FilesystemTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TempFileAdapterTest extends FilesystemTest
{
    use AccessRealFileTests;

    protected function createFilesystem(): Filesystem
    {
        return (new FilesystemFactory())->create('temp-file(in-memory:)');
    }
}
