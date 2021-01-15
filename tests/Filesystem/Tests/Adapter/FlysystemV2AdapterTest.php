<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\FlysystemV2Adapter;
use Zenstruck\Filesystem\Tests\Feature\CopyFileTests;
use Zenstruck\Filesystem\Tests\Feature\CreateDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\DeleteDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\DeleteFileTests;
use Zenstruck\Filesystem\Tests\Feature\MoveFileTests;
use Zenstruck\Filesystem\Tests\Feature\ReadDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\WriteFileTests;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FlysystemV2AdapterTest extends AdapterTest
{
    use CopyFileTests, CreateDirectoryTests, DeleteDirectoryTests, DeleteFileTests, MoveFileTests, ReadDirectoryTests, WriteFileTests;

    private const ROOT = __DIR__.'/../../../../var/flysystem2-filesystem';

    protected function setUp(): void
    {
        parent::setUp();

        if (!\interface_exists(FilesystemOperator::class)) {
            $this->markTestSkipped('Flysystem V2 not available.');
        }

        (new SymfonyFilesystem())->remove(self::ROOT);
    }

    protected function createAdapter(): Adapter
    {
        return new FlysystemV2Adapter(new Filesystem(new LocalFilesystemAdapter(self::ROOT)));
    }
}
