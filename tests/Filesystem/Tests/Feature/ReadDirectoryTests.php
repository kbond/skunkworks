<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Exception\NodeTypeMismatch;
use Zenstruck\Filesystem\Node;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait ReadDirectoryTests
{
    /**
     * @test
     */
    public function can_check_if_directory_exists(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->mkdir('subdir');

        $this->assertTrue($filesystem->exists('subdir'));
        $this->assertFalse($filesystem->exists('non-existent'));
    }

    /**
     * @test
     */
    public function can_get_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file1.txt', 'file1');
        $filesystem->write('subdir/file2.txt', 'file2');
        $filesystem->write('subdir/nested/file3.txt', 'file3');

        $dir = $filesystem->directory('/subdir');

        $this->assertTrue($dir->isDirectory());
        $this->assertCount(3, $dir);

        /** @var Node[] $listing */
        $listing = \iterator_to_array($dir);
        \usort($listing, static fn(Node $a, Node $b) => \strcmp($a->path(), $b->path()));

        $this->assertCount(3, $listing);
        $this->assertTrue($listing[0]->isFile());

        $this->assertTrue($listing[1]->isFile());
        $this->assertSame('file2', $listing[1]->contents());
        $this->assertIsResource($listing[1]->read());

        $this->assertTrue($listing[2]->isDirectory());

        $this->assertCount(2, $dir->files());
        $this->assertCount(1, $dir->directories());
    }

    /**
     * @test
     */
    public function can_get_empty_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->mkdir('foo/bar');

        $this->assertCount(0, $filesystem->directory('foo/bar'));
    }

    /**
     * @test
     */
    public function cannot_get_directory_for_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file1.txt', 'file1');

        $this->expectException(NodeTypeMismatch::class);

        $filesystem->directory('subdir/file1.txt');
    }

    /**
     * @test
     */
    public function cannot_get_file_for_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file1.txt', 'file1');

        $this->expectException(NodeTypeMismatch::class);

        $filesystem->file('subdir');
    }

    /**
     * @test
     */
    public function can_get_directory_recursive(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file1.txt', 'file1');
        $filesystem->write('subdir/file2.txt', 'file2');
        $filesystem->write('subdir/nested/file3.txt', 'file3');
        $filesystem->write('subdir/nested/nested1/file4.txt', 'file4');
        $filesystem->mkdir('subdir/sub');
        $filesystem->mkdir('subdir/sub/sub2');
        $filesystem->mkdir('subdir/sub/sub2/sub3');

        $dir = $filesystem->directory('subdir');

        $this->assertCount(9, \iterator_to_array($dir->recursive()));
        $this->assertCount(4, \iterator_to_array($dir->recursive()->files()));
        $this->assertCount(5, \iterator_to_array($dir->recursive()->directories()));
    }

    /**
     * @test
     */
    public function can_get_root_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file1.txt', 'file1');
        $filesystem->write('subdir/file2.txt', 'file2');
        $filesystem->write('subdir/nested/file3.txt', 'file3');

        $root = $filesystem->directory();

        $this->assertTrue($root->isDirectory());
        $this->assertCount(1, $root);
    }

    /**
     * @test
     */
    public function can_check_if_root_exists(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file1.txt', 'file1');

        $this->assertTrue($filesystem->exists());
    }

    abstract protected function createFilesystem(): Filesystem;
}
