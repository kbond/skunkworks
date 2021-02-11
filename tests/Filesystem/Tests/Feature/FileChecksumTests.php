<?php

namespace Zenstruck\Filesystem\Tests\Feature;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait FileChecksumTests
{
    /**
     * @test
     */
    public function can_get_file_checksum(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file1.txt', 'contents');
        $filesystem->write('file2.txt', 'contents');
        $filesystem->write('file3.txt', 'different contents');

        $file1checksum = $filesystem->file('file1.txt')->checksum();

        $this->assertSame($file1checksum, $filesystem->file('file2.txt')->checksum());
        $this->assertNotSame($file1checksum, $filesystem->file('file3.txt')->checksum());
    }
}
