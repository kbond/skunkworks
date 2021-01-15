<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Exception\RuntimeException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait MoveFileTests
{
    /**
     * @test
     */
    public function can_move_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'contents');

        $this->assertTrue($filesystem->exists('file.txt'));
        $this->assertSame('contents', $filesystem->file('file.txt')->contents());

        $filesystem->move('file.txt', 'new-file.txt');

        $this->assertFalse($filesystem->exists('file.txt'));
        $this->assertTrue($filesystem->exists('new-file.txt'));
        $this->assertSame('contents', $filesystem->file('new-file.txt')->contents());
    }

    /**
     * @test
     */
    public function can_move_file_over_existing_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file1.txt', 'file1');
        $filesystem->write('file2.txt', 'file2');

        $this->assertSame('file2', $filesystem->file('file2.txt')->contents());

        $filesystem->move('file1.txt', 'file2.txt');

        $this->assertSame('file1', $filesystem->file('file2.txt')->contents());
    }

    /**
     * @test
     */
    public function cannot_move_file_to_existing_dir(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'file1');
        $filesystem->mkdir('dir');

        try {
            $filesystem->move('file.txt', 'dir');
        } catch (RuntimeException $e) {
            $this->assertTrue($filesystem->node('dir')->isDirectory());
            $this->assertTrue($filesystem->node('file.txt')->isFile());

            return;
        }

        $this->fail('Exception not thrown.');
    }

    abstract protected function createFilesystem(): Filesystem;
}
