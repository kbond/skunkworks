<?php

namespace Zenstruck\Filesystem\Tests;

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
        $filesystem = (new FilesystemFactory())->create('flysystem+ftp://user:pass@ftp.example.com');

        $this->assertInstanceOf(AdapterFilesystem::class, $filesystem);
    }
}
