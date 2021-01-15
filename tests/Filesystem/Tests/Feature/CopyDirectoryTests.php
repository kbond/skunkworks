<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Exception\RuntimeException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait CopyDirectoryTests
{
    /**
     * @test
     */
    public function cannot_copy_dir_to_existing_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'file1');
        $filesystem->mkdir('dir');

        $this->expectException(RuntimeException::class);

        $filesystem->copy('dir', 'file.txt');
    }

    /**
     * @test
     */
    public function can_copy_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir1/file1.txt', 'file1');
        $filesystem->write('subdir1/nested/file2.txt', 'file2');

        $this->assertTrue($filesystem->exists('subdir1'));
        $this->assertFalse($filesystem->exists('subdir2'));
        $this->assertSame('file1', $filesystem->file('subdir1/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir1/nested/file2.txt')->contents());

        $filesystem->copy('subdir1', 'subdir2');

        $this->assertTrue($filesystem->exists('subdir1'));
        $this->assertTrue($filesystem->exists('subdir2'));
        $this->assertSame('file1', $filesystem->file('subdir1/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir1/nested/file2.txt')->contents());
        $this->assertSame('file1', $filesystem->file('subdir2/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir2/nested/file2.txt')->contents());
    }

    /**
     * @test
     */
    public function can_copy_directory_over_existing_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir1/file1.txt', 'file1');
        $filesystem->write('subdir1/file2.txt', 'file2');
        $filesystem->write('subdir2/file3.txt', 'file3');

        $this->assertSame('file1', $filesystem->file('subdir1/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir1/file2.txt')->contents());
        $this->assertTrue($filesystem->exists('subdir2/file3.txt'));

        $filesystem->copy('subdir1', 'subdir2');

        $this->assertTrue($filesystem->exists('subdir1'));
        $this->assertTrue($filesystem->exists('subdir2'));
        $this->assertSame('file1', $filesystem->file('subdir1/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir1/file2.txt')->contents());
        $this->assertSame('file1', $filesystem->file('subdir2/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir2/file2.txt')->contents());
        $this->assertFalse($filesystem->exists('subdir2/file3.txt'));
    }

    abstract protected function createFilesystem(): Filesystem;
}
