<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Exception\RuntimeException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait MoveDirectoryTests
{
    /**
     * @test
     */
    public function cannot_move_dir_to_existing_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'file1');
        $filesystem->mkdir('dir');

        try {
            $filesystem->move('dir', 'file.txt');
        } catch (RuntimeException $e) {
            $this->assertTrue($filesystem->node('dir')->isDirectory());
            $this->assertTrue($filesystem->node('file.txt')->isFile());

            return;
        }

        $this->fail('Exception not thrown.');
    }

    /**
     * @test
     */
    public function can_move_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file1.txt', 'file1');
        $filesystem->write('subdir/file2.txt', 'file2');

        $this->assertTrue($filesystem->exists('subdir'));
        $this->assertSame('file1', $filesystem->file('subdir/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir/file2.txt')->contents());

        $filesystem->move('subdir', 'new-subdir');

        $this->assertFalse($filesystem->exists('subdir'));
        $this->assertTrue($filesystem->exists('new-subdir'));
        $this->assertSame('file1', $filesystem->file('new-subdir/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('new-subdir/file2.txt')->contents());
    }

    /**
     * @test
     */
    public function can_move_directory_over_existing_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir1/file1.txt', 'file1');
        $filesystem->write('subdir1/file2.txt', 'file2');
        $filesystem->write('subdir2/file3.txt', 'file3');

        $this->assertSame('file1', $filesystem->file('subdir1/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir1/file2.txt')->contents());
        $this->assertTrue($filesystem->exists('subdir2/file3.txt'));

        $filesystem->move('subdir1', 'subdir2');

        $this->assertFalse($filesystem->exists('subdir1'));
        $this->assertTrue($filesystem->exists('subdir2'));
        $this->assertSame('file1', $filesystem->file('subdir2/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir2/file2.txt')->contents());
        $this->assertFalse($filesystem->exists('subdir2/file3.txt'));
    }

    abstract protected function createFilesystem(): Filesystem;
}
