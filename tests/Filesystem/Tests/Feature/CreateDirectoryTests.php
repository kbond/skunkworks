<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Exception\RuntimeException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait CreateDirectoryTests
{
    /**
     * @test
     */
    public function can_make_directory(): void
    {
        $filesystem = $this->createFilesystem();

        $this->assertFalse($filesystem->exists('subdir'));

        $filesystem->mkdir('/subdir');

        $this->assertTrue($filesystem->exists('subdir'));
        $this->assertTrue($filesystem->node('subdir')->isDirectory());
    }

    /**
     * @test
     */
    public function can_make_nested_directory(): void
    {
        $filesystem = $this->createFilesystem();

        $this->assertFalse($filesystem->exists('subdir'));
        $this->assertFalse($filesystem->exists('subdir/nested'));

        $filesystem->mkdir('/subdir/nested');

        $this->assertTrue($filesystem->exists('subdir'));
        $this->assertTrue($filesystem->node('subdir')->isDirectory());
        $this->assertTrue($filesystem->exists('subdir/nested'));
        $this->assertTrue($filesystem->node('subdir/nested')->isDirectory());
    }

    /**
     * @test
     */
    public function making_directory_that_already_exists_keeps_files(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file1.txt', 'file1');
        $filesystem->write('subdir/file2.txt', 'file2');

        $this->assertSame('file1', $filesystem->file('subdir/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir/file2.txt')->contents());

        $filesystem->mkdir('subdir');

        $this->assertSame('file1', $filesystem->file('subdir/file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('subdir/file2.txt')->contents());
    }

    /**
     * @test
     */
    public function cannot_make_directory_if_file_exists(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file', 'contents');

        try {
            $filesystem->mkdir('file');
        } catch (RuntimeException $e) {
            $this->assertSame('contents', $filesystem->file('file')->contents());

            return;
        }

        $this->fail('Exception not thrown.');
    }

    abstract protected function createFilesystem(): Filesystem;
}
