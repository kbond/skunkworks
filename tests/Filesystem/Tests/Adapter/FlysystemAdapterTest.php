<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use League\Flysystem\Filesystem as FlysystemFilesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Adapter\FlysystemAdapter;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\Tests\Feature\CopyFileTests;
use Zenstruck\Filesystem\Tests\Feature\CreateDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\DeleteDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\DeleteFileTests;
use Zenstruck\Filesystem\Tests\Feature\MoveFileTests;
use Zenstruck\Filesystem\Tests\Feature\ReadDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\WriteFileTests;
use Zenstruck\Filesystem\Tests\FilesystemTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FlysystemAdapterTest extends FilesystemTest
{
    use CopyFileTests, CreateDirectoryTests, DeleteDirectoryTests, DeleteFileTests, MoveFileTests, ReadDirectoryTests, WriteFileTests;

    private const ROOT = __DIR__.'/../../../../var/flysystem-filesystem';

    protected function setUp(): void
    {
        parent::setUp();

        if (!\interface_exists(FilesystemOperator::class)) {
            $this->markTestSkipped('Flysystem V2 not available.');
        }

        (new SymfonyFilesystem())->remove(self::ROOT);
    }

    protected function createFilesystem(): Filesystem
    {
        return new AdapterFilesystem(new FlysystemAdapter(new FlysystemFilesystem(new LocalFilesystemAdapter(self::ROOT))));
    }
}
