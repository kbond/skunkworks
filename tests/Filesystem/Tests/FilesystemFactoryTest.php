<?php

namespace Zenstruck\Filesystem\Tests;

use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Ftp\FtpAdapter;
use PHPUnit\Framework\TestCase;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\FilesystemFactory;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FilesystemFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function can_create_flysystem_ftp_filesystem(): void
    {
        if (!\class_exists(FtpAdapter::class) && !\class_exists(Ftp::class)) {
            $this->markTestSkipped('Flysystem FTP adapter not available.');
        }

        $filesystem = (new FilesystemFactory())->create('flysystem+ftp://user:pass@ftp.example.com');

        $this->assertInstanceOf(AdapterFilesystem::class, $filesystem);
    }
}
