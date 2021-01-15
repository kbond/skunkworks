<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait AccessRealFileTests
{
    /**
     * @test
     */
    public function can_get_real_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'contents');

        $file = $filesystem->file('/dir/../file.txt')->real();

        $this->assertTrue($file->isFile());
        $this->assertTrue($file->isReadable());
        $this->assertSame('contents', \file_get_contents($file));
        $this->assertSame(8, $file->getSize());
    }

    abstract protected function createFilesystem(): Filesystem;
}
