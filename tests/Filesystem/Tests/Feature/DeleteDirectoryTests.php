<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait DeleteDirectoryTests
{
    /**
     * @test
     */
    public function can_remove_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file.txt', 'file1');

        $this->assertTrue($filesystem->exists('/subdir/file.txt'));
        $this->assertTrue($filesystem->exists('/subdir'));

        $filesystem->delete('subdir');

        $this->assertFalse($filesystem->exists('/subdir/file.txt'));
        $this->assertFalse($filesystem->exists('/subdir'));
    }

    /**
     * @test
     */
    public function can_remove_empty_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->mkdir('subdir');

        $this->assertTrue($filesystem->exists('/subdir'));

        $filesystem->delete('subdir');

        $this->assertFalse($filesystem->exists('/subdir'));
    }

    /**
     * @test
     */
    public function can_delete_root(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'contents');

        $this->assertTrue($filesystem->exists());
        $this->assertTrue($filesystem->exists('file.txt'));

        $filesystem->delete(); // delete root

        $this->assertFalse($filesystem->exists('file.txt'));
        $this->assertFalse($filesystem->exists());
    }

    abstract protected function createFilesystem(): Filesystem;
}
