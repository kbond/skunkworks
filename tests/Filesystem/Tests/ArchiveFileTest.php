<?php

namespace Zenstruck\Filesystem\Tests;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Zenstruck\Filesystem\ArchiveFile;
use Zenstruck\Filesystem\Tests\Feature\CreateDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\DeleteDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\DeleteFileTests;
use Zenstruck\Filesystem\Tests\Feature\ReadDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\WriteFileTests;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ArchiveFileTest extends FilesystemTest
{
    use CreateDirectoryTests, DeleteDirectoryTests, DeleteFileTests, ReadDirectoryTests, WriteFileTests;

    private const FILE = __DIR__.'/../../../../var/archive.zip';

    protected function setUp(): void
    {
        parent::setUp();

        (new SymfonyFilesystem())->remove(self::FILE);
    }

    /**
     * @test
     */
    public function can_create_archive_file_in_non_existent_directory(): void
    {
        (new SymfonyFilesystem())->remove(\dirname(self::FILE));

        $filesystem = $this->createFilesystem();
        $filesystem->write('foo.txt', 'contents');

        $this->assertFileExists(self::FILE);
    }

    /**
     * @test
     */
    public function deleting_root_deletes_archive(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('foo.txt', 'contents');

        $this->assertFileExists(self::FILE);

        $filesystem->delete();

        $this->assertFileDoesNotExist(self::FILE);
    }

    /**
     * @test
     */
    public function trying_to_read_from_non_existent_archive_does_not_create_the_file(): void
    {
        $filesystem = $this->createFilesystem();

        $this->assertFileDoesNotExist(self::FILE);

        $this->assertFalse($filesystem->exists('foo.txt'));

        $this->assertFileDoesNotExist(self::FILE);
    }

    /**
     * @test
     */
    public function cannot_open_invalid_zip(): void
    {
        \file_put_contents(self::FILE, 'not-a-zip');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not open');

        $this->createFilesystem()->exists('foo');
    }

    /**
     * @test
     */
    public function can_create_without_path_to_create_temp_file(): void
    {
        $filesystem = new ArchiveFile();
        $filesystem->write('file.txt', 'contents');

        $this->assertTrue($filesystem->exists());
        $this->assertTrue($filesystem->exists('file.txt'));
        $this->assertSame('contents', $filesystem->file('file.txt')->contents());
    }

    /**
     * @test
     */
    public function can_read_existing_file(): void
    {
        $filesystem = new ArchiveFile(__DIR__.'/Fixture/archive.zip');

        $this->assertTrue($filesystem->exists());
        $this->assertTrue($filesystem->exists('file1.txt'));
        $this->assertTrue($filesystem->exists('nested/file2.txt'));
        $this->assertSame('contents 2', $filesystem->file('nested/file2.txt')->contents());
    }

    /**
     * @test
     */
    public function can_wrap_write_operations_in_a_transaction(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->beginTransaction();
        $filesystem->write('file1.txt', 'contents1');
        $filesystem->write('sub/file2.txt', 'contents2');

        $this->assertFalse($filesystem->exists('file1.txt'));
        $this->assertFalse($filesystem->exists('sub/file2.txt'));

        $filesystem->commit();

        $this->assertTrue($filesystem->exists('file1.txt'));
        $this->assertTrue($filesystem->exists('sub/file2.txt'));
    }

    /**
     * @test
     */
    public function can_use_commit_callback(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->beginTransaction();
        $filesystem->write('file1.txt', 'contents1');
        $filesystem->write('sub/file2.txt', 'contents2');

        $this->assertFalse($filesystem->exists('file1.txt'));
        $this->assertFalse($filesystem->exists('sub/file2.txt'));

        foreach (\array_fill(0, 20, 'contents') as $key => $value) {
            $filesystem->write("many/file{$key}.txt", $value);
        }

        $actualFirst = null;
        $actualLast = null;
        $count = 0;

        $filesystem->commit(function($current) use (&$actualFirst, &$actualLast, &$count) {
            if (null === $actualFirst) {
                $actualFirst = $current;
            }

            $actualLast = $current;
            ++$count;
        });

        $this->assertTrue($filesystem->exists('file1.txt'));
        $this->assertTrue($filesystem->exists('sub/file2.txt'));

        if (\PHP_MAJOR_VERSION < 8) {
            $this->assertNull($actualFirst);
            $this->assertNull($actualLast);
            $this->assertSame(0, $count);

            return;
        }

        $this->assertSame(23, $count);
        $this->assertSame(0, (int) $actualFirst);
        $this->assertSame(1, (int) $actualLast);
    }

    protected function createFilesystem(): ArchiveFile
    {
        return new ArchiveFile(self::FILE);
    }
}
