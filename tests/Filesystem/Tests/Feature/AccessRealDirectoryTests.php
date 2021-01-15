<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait AccessRealDirectoryTests
{
    /**
     * @test
     */
    public function can_get_real_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->mkdir('some/dir');

        $dir = $filesystem->directory('some/dir')->real();

        $this->assertTrue($dir->isDir());
    }

    abstract protected function createFilesystem(): Filesystem;
}
