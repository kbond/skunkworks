<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Exception\NotFound;
use Zenstruck\Filesystem\Exception\RuntimeException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait CopyFileTests
{
    /**
     * @test
     */
    public function can_copy_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file1.txt', 'contents');

        $this->assertFalse($filesystem->exists('file2.txt'));

        $filesystem->copy('/file1.txt', 'file2.txt');

        $this->assertTrue($filesystem->exists('file2.txt'));
        $this->assertSame('contents', $filesystem->file('file2.txt')->contents());
    }

    /**
     * @test
     */
    public function can_copy_file_over_existing_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file1.txt', 'file1');
        $filesystem->write('file2.txt', 'file2');

        $this->assertSame('file1', $filesystem->file('file1.txt')->contents());
        $this->assertSame('file2', $filesystem->file('file2.txt')->contents());

        $filesystem->copy('/file1.txt', 'file2.txt');

        $this->assertSame('file1', $filesystem->file('file1.txt')->contents());
        $this->assertSame('file1', $filesystem->file('file2.txt')->contents());
    }

    /**
     * @test
     */
    public function cannot_copy_file_to_existing_dir(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'file1');
        $filesystem->mkdir('dir');

        try {
            $filesystem->copy('file.txt', 'dir');
        } catch (RuntimeException $e) {
            $this->assertTrue($filesystem->node('dir')->isDirectory());

            return;
        }

        $this->fail('Exception not thrown.');
    }

    /**
     * @test
     */
    public function cannot_copy_non_existent_source_key(): void
    {
        $this->expectException(NotFound::class);

        $this->createFilesystem()->copy('non-existent', 'file.txt');
    }

    abstract protected function createFilesystem(): Filesystem;
}
