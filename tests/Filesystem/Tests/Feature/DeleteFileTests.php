<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait DeleteFileTests
{
    /**
     * @test
     */
    public function can_remove_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file.txt', 'file1');

        $this->assertTrue($filesystem->exists('/subdir/file.txt'));

        $filesystem->delete('subdir/file.txt');

        $this->assertFalse($filesystem->exists('/subdir/file.txt'));
    }

    abstract protected function createFilesystem(): Filesystem;
}
