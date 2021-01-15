<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\StreamAdapter;
use Zenstruck\Filesystem\TempFile;
use Zenstruck\Filesystem\Tests\Feature\AccessRealDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\AccessRealFileTests;
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
final class StreamAdapterTest extends AdapterTest
{
    use AccessRealDirectoryTests, AccessRealFileTests, CopyDirectoryTests, CopyFileTests, CreateDirectoryTests, DeleteDirectoryTests, DeleteFileTests, MoveDirectoryTests, MoveFileTests, ReadDirectoryTests, WriteFileTests;

    private const ROOT = __DIR__.'/../../../../var/stream-filesystem';

    protected function setUp(): void
    {
        parent::setUp();

        (new SymfonyFilesystem())->remove(self::ROOT);
    }

    /**
     * @test
     */
    public function writing_temp_file_moves_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file1.txt', 'contents');
        $file = $filesystem->file('file1.txt');

        $this->assertTrue($filesystem->exists($file));

        $filesystem->write('file2.txt', new TempFile($file->real()));

        $newFile = $filesystem->file('file2.txt');

        $this->assertFalse($filesystem->exists($file));
        $this->assertTrue($filesystem->exists($newFile));
        $this->assertSame('contents', $newFile->contents());
    }

    protected function createAdapter(): Adapter
    {
        return new StreamAdapter(self::ROOT);
    }
}
