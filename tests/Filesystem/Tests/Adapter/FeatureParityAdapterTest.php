<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Adapter\FeatureParityAdapter;
use Zenstruck\Filesystem\Adapter\FlysystemAdapter;
use Zenstruck\Filesystem\Adapter\InMemoryAdapter;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\Exception\UnsupportedFeature;
use Zenstruck\Filesystem\Feature\DeleteDirectory;
use Zenstruck\Filesystem\Feature\FileChecksum;
use Zenstruck\Filesystem\Tests\FilesystemTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FeatureParityAdapterTest extends FilesystemTest
{
    /**
     * @test
     */
    public function supports_returns_false_for_feature_check_adapter_does_not_support(): void
    {
        $filesystem = $this->createFilesystem();

        $this->assertTrue($filesystem->supports(DeleteDirectory::class));
        $this->assertFalse($filesystem->supports(FileChecksum::class));
    }

    /**
     * @test
     */
    public function using_a_feature_not_supported_by_check_adapter_fails(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('/some/file.txt', 'contents');

        $file = $filesystem->file('/some/file.txt');

        $this->expectException(UnsupportedFeature::class);

        $file->checksum();
    }

    protected function createFilesystem(): Filesystem
    {
        return new AdapterFilesystem(new FeatureParityAdapter(
            new InMemoryAdapter(),
            new FlysystemAdapter(new Flysystem(new LocalFilesystemAdapter(__DIR__)))
        ));
    }
}
