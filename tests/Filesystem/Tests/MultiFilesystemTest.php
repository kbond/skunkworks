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

    /**
     * @test
     */
    public function can_copy_files_across_filesystems(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('first://foo/bar.txt', 'contents');

        $this->assertTrue($filesystem->exists('first://foo/bar.txt'));
        $this->assertFalse($filesystem->exists('second://baz/bar.txt'));

        $filesystem->copy('first://foo/bar.txt', 'second://baz/bar.txt');

        $this->assertTrue($filesystem->exists('first://foo/bar.txt'));
        $this->assertTrue($filesystem->exists('second://baz/bar.txt'));
    }

    /**
     * @test
     */
    public function can_move_files_across_filesystems(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('first://foo/bar.txt', 'contents');

        $this->assertTrue($filesystem->exists('first://foo/bar.txt'));
        $this->assertFalse($filesystem->exists('second://baz/bar.txt'));

        $filesystem->move('first://foo/bar.txt', 'second://baz/bar.txt');

        $this->assertFalse($filesystem->exists('first://foo/bar.txt'));
        $this->assertTrue($filesystem->exists('second://baz/bar.txt'));
    }

    /**
     * @test
     */
    public function can_copy_directories_across_filesystems(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('first://foo/bar.txt', 'contents');
        $filesystem->write('first://foo/nested/bar.txt', 'contents');

        $this->assertTrue($filesystem->exists('first://foo/bar.txt'));
        $this->assertTrue($filesystem->exists('first://foo/nested/bar.txt'));
        $this->assertFalse($filesystem->exists('second://baz/bar.txt'));
        $this->assertFalse($filesystem->exists('second://baz/nested/bar.txt'));

        $filesystem->copy('first://foo', 'second://baz');

        $this->assertTrue($filesystem->exists('first://foo/bar.txt'));
        $this->assertTrue($filesystem->exists('first://foo/nested/bar.txt'));
        $this->assertTrue($filesystem->exists('second://baz/bar.txt'));
        $this->assertTrue($filesystem->exists('second://baz/nested/bar.txt'));
    }

    /**
     * @test
     */
    public function can_move_directories_across_filesystems(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('first://foo/bar.txt', 'contents');
        $filesystem->write('first://foo/nested/bar.txt', 'contents');

        $this->assertTrue($filesystem->exists('first://foo/bar.txt'));
        $this->assertTrue($filesystem->exists('first://foo/nested/bar.txt'));
        $this->assertFalse($filesystem->exists('second://baz/bar.txt'));
        $this->assertFalse($filesystem->exists('second://baz/nested/bar.txt'));

        $filesystem->move('first://foo', 'second://baz');

        $this->assertFalse($filesystem->exists('first://foo/bar.txt'));
        $this->assertFalse($filesystem->exists('first://foo/nested/bar.txt'));
        $this->assertTrue($filesystem->exists('second://baz/bar.txt'));
        $this->assertTrue($filesystem->exists('second://baz/nested/bar.txt'));
    }

    protected function createFilesystem(): Filesystem
    {
        return $this->createForArray([
            'first' => new AdapterFilesystem(new InMemoryAdapter()),
            'second' => new AdapterFilesystem(new InMemoryAdapter()),
        ]);
    }

    abstract protected function createForArray(array $filesystems): MultiFilesystem;
}
