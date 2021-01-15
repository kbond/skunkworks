<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\FlysystemV1Adapter;
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
final class FlysystemV1AdapterTest extends AdapterTest
{
    use CopyFileTests, CreateDirectoryTests, DeleteDirectoryTests, DeleteFileTests, MoveFileTests, ReadDirectoryTests, WriteFileTests;

    private const ROOT = __DIR__.'/../../../../var/flysystem1-filesystem';

    protected function setUp(): void
    {
        parent::setUp();

        if (!\interface_exists(FilesystemInterface::class)) {
            $this->markTestSkipped('Flysystem V1 not available.');
        }

        (new SymfonyFilesystem())->remove(self::ROOT);
    }

    /**
     * @test
     */
    public function can_delete_root(): void
    {
        $this->markTestSkipped('Flysystem V1 cannot delete root directories.');
    }

    protected function createAdapter(): Adapter
    {
        return new FlysystemV1Adapter(new Filesystem(new Local(self::ROOT)));
    }
}
