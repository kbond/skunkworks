<?php

namespace Zenstruck\Filesystem\Tests;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Adapter\InMemoryAdapter;
use Zenstruck\Filesystem\Adapter\UrlPrefixAdapter;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\ScopedFilesystem;
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
final class ScopedFilesystemTest extends FilesystemTest
{
    use CopyDirectoryTests, CopyFileTests, CreateDirectoryTests, DeleteDirectoryTests, DeleteFileTests, MoveDirectoryTests, MoveFileTests, ReadDirectoryTests, WriteFileTests;

    /**
     * @test
     */
    public function node_path_is_scoped(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('some/file.txt', 'contents');

        $this->assertSame('/scope/prefix/some/file.txt', $filesystem->file('some/file.txt')->path());
    }

    /**
     * @test
     */
    public function url_is_scoped(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('some/file.txt', 'contents');

        $this->assertSame('https://example.com/sub/scope/prefix/some/file.txt', $filesystem->file('some/file.txt')->url()->toString());
    }

    protected function createFilesystem(): Filesystem
    {
        return new ScopedFilesystem(
            new AdapterFilesystem(new UrlPrefixAdapter(new InMemoryAdapter(), 'https://example.com/sub')),
            'scope/prefix'
        );
    }
}
