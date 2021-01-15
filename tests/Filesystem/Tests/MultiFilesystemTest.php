<?php

namespace Zenstruck\Filesystem\Tests;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Adapter\InMemoryAdapter;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\MultiFilesystem;
use Zenstruck\Filesystem\Tests\Feature\CopyDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\CopyFileTests;
use Zenstruck\Filesystem\Tests\Feature\CreateDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\DeleteDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\DeleteFileTests;
use Zenstruck\Filesystem\Tests\Feature\MoveDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\MoveFileTests;
use Zenstruck\Filesystem\Tests\Feature\ReadDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\WriteFileTests;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class MultiFilesystemTest extends FilesystemTest
{
    use CopyDirectoryTests, CopyFileTests, CreateDirectoryTests, DeleteDirectoryTests, DeleteFileTests, MoveDirectoryTests, MoveFileTests, ReadDirectoryTests, WriteFileTests;

    protected function createFilesystem(): Filesystem
    {
        return $this->createForArray([
            'first' => new AdapterFilesystem(new InMemoryAdapter()),
            'second' => new AdapterFilesystem(new InMemoryAdapter()),
        ]);
    }

    abstract protected function createForArray(array $filesystems): MultiFilesystem;
}
