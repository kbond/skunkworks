<?php

namespace Zenstruck\Filesystem\Tests;

use Zenstruck\Filesystem\Adapter\InMemoryAdapter;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\TestFilesystem;
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
final class TestFilesystemTest extends FilesystemTest
{
    use CopyDirectoryTests, CopyFileTests, CreateDirectoryTests, DeleteDirectoryTests, DeleteFileTests, MoveDirectoryTests, MoveFileTests, ReadDirectoryTests, WriteFileTests;

    /**
     * @test
     */
    public function can_use_assertions(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file1.txt', 'contents1');
        $filesystem->write('file2.txt', 'contents2');
        $filesystem->write('file3.txt', 'contents1');

        $filesystem
            ->assertExists('file1.txt')
            ->assertNotExists('file4.txt')
            ->assertContents('file1.txt', 'contents1')
            ->assertNotContents('file1.txt', 'contents2')
            ->assertContentsContains('file1.txt', 'contents')
            ->assertContentsNotContains('file1.txt', 'other')
            ->assertSame('file1.txt', 'file3.txt')
            ->assertNotSame('file1.txt', 'file2.txt')
        ;
    }

    protected function createFilesystem(): TestFilesystem
    {
        return new TestFilesystem(new AdapterFilesystem(new InMemoryAdapter()));
    }
}
