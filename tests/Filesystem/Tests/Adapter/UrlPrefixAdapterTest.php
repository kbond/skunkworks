<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\InMemoryAdapter;
use Zenstruck\Filesystem\Adapter\UrlPrefixAdapter;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\Tests\Feature\CopyDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\CopyFileTests;
use Zenstruck\Filesystem\Tests\Feature\CreateDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\DeleteDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\DeleteFileTests;
use Zenstruck\Filesystem\Tests\Feature\MoveDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\MoveFileTests;
use Zenstruck\Filesystem\Tests\Feature\ReadDirectoryTests;
use Zenstruck\Filesystem\Tests\Feature\WriteFileTests;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlPrefixAdapterTest extends AdapterTest
{
    use CopyDirectoryTests, CopyFileTests, CreateDirectoryTests, DeleteDirectoryTests, DeleteFileTests, MoveDirectoryTests, MoveFileTests, ReadDirectoryTests, WriteFileTests;

    /**
     * @test
     */
    public function can_access_node_url(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('nested/file.txt', 'contents');

        $this->assertSame('https://example.com/sub/nested/file.txt', $filesystem->node('nested/file.txt')->url()->toString());
        $this->assertSame('https://example.com/sub/nested/file.txt', $filesystem->node('/nested/file.txt')->url()->toString());
        $this->assertSame('https://example.com/sub/nested/file.txt', $filesystem->node('nested/file.txt')->url()->toString());
        $this->assertSame('https://example.com/sub/nested', $filesystem->node('nested')->url()->toString());
        $this->assertSame('https://example.com/sub/nested', $filesystem->node('/nested')->url()->toString());
    }

    /**
     * @test
     */
    public function can_use_multiple_prefixes_to_provide_a_deterministic_distribution_strategy(): void
    {
        $filesystem = new AdapterFilesystem(new UrlPrefixAdapter(new InMemoryAdapter(), 'https://sub1.example.com', 'https://sub2.example.com'));
        $filesystem->write('file1.txt', 'contents');
        $filesystem->write('file2.txt', 'contents');

        $this->assertSame('https://sub2.example.com/file1.txt', $filesystem->file('file1.txt')->url()->toString());
        $this->assertSame('https://sub1.example.com/file2.txt', $filesystem->file('file2.txt')->url()->toString());
    }

    protected function createAdapter(): Adapter
    {
        return new UrlPrefixAdapter(new InMemoryAdapter(), 'https://example.com/sub');
    }
}
