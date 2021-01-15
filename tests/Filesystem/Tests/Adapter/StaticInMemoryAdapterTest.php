<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\StaticInMemoryAdapter;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\Test\ResetStaticInMemoryAdapter;
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
final class StaticInMemoryAdapterTest extends AdapterTest
{
    use CopyDirectoryTests, CopyFileTests, CreateDirectoryTests, DeleteDirectoryTests, DeleteFileTests, MoveDirectoryTests, MoveFileTests, ReadDirectoryTests, ResetStaticInMemoryAdapter, WriteFileTests;

    /**
     * @test
     */
    public function state_is_persisted_between_objects(): void
    {
        $filesystem1 = $this->createFilesystem();
        $filesystem2 = $this->createFilesystem();
        $filesystem1->write('file.txt', 'contents');

        $this->assertTrue($filesystem1->exists('file.txt'));
        $this->assertTrue($filesystem2->exists('file.txt'));
    }

    /**
     * @test
     */
    public function state_is_kept_by_name(): void
    {
        $filesystem1 = new AdapterFilesystem(new StaticInMemoryAdapter('first'));
        $filesystem2 = new AdapterFilesystem(new StaticInMemoryAdapter('second'));
        $filesystem1->write('file.txt', 'contents');

        $this->assertTrue($filesystem1->exists('file.txt'));
        $this->assertFalse($filesystem2->exists('file.txt'));
    }

    protected function createAdapter(): Adapter
    {
        return new StaticInMemoryAdapter();
    }
}
