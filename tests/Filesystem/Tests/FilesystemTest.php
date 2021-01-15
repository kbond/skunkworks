<?php

namespace Zenstruck\Filesystem\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Exception\NotFound;
use Zenstruck\Filesystem\Exception\PathOutsideRoot;
use Zenstruck\Filesystem\Node\Directory;
use Zenstruck\Filesystem\Node\File;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class FilesystemTest extends TestCase
{
    /**
     * @test
     */
    public function can_check_if_file_exists(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'file1');

        $this->assertTrue($filesystem->exists('file.txt'));
        $this->assertFalse($filesystem->exists('non-existent'));
    }

    /**
     * @test
     */
    public function cannot_get_non_existent_key(): void
    {
        $this->expectException(NotFound::class);

        $this->createFilesystem()->node('non-existent');
    }

    /**
     * @test
     */
    public function can_get_nodes(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file1.txt', 'file1');
        $filesystem->write('subdir/file2.txt', 'file2');
        $filesystem->write('subdir/nested/file3.txt', 'file3');

        $file = $filesystem->node('/subdir/file1.txt');

        $this->assertInstanceOf(File::class, $file);
        $this->assertTrue($file->isFile());

        /** @var Directory $dir */
        $dir = $filesystem->node('/subdir');

        $this->assertInstanceOf(Directory::class, $dir);
        $this->assertTrue($dir->isDirectory());
    }

    /**
     * @test
     */
    public function can_get_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('subdir/file.txt', 'contents');

        $file = $filesystem->file('/subdir/file.txt');

        $this->assertTrue($file->isFile());
        $this->assertSame('file.txt', $file->filename());
        $this->assertSame('txt', $file->extension());
        $this->assertSame('text/plain', $file->mimeType());
        $this->assertSame(8, $file->size());
        $this->assertSame((new \DateTime())->format('Y-m-d O'), $file->lastModified()->format('Y-m-d O'));
        $this->assertSame('contents', $file->contents());
        $this->assertIsResource($file->read());
    }

    /**
     * @test
     */
    public function removing_non_existent_key_does_nothing(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'contents');

        $filesystem->delete('non-existent');

        $this->assertTrue($filesystem->exists('file.txt'));
    }

    /**
     * @test
     */
    public function cannot_move_non_existent_source_key(): void
    {
        $this->expectException(NotFound::class);

        $this->createFilesystem()->move('non-existent', 'file.txt');
    }

    /**
     * @test
     */
    public function can_read_file_as_resource(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'contents');

        $this->assertIsResource($resource = $filesystem->file('file.txt')->read());
        $this->assertSame('contents', \stream_get_contents($resource));
    }

    /**
     * @test
     */
    public function cannot_get_outside_of_root(): void
    {
        $this->expectException(PathOutsideRoot::class);

        $this->createFilesystem()->node('../../../some-file.txt');
    }

    /**
     * @test
     */
    public function cannot_copy_from_source_outside_of_root(): void
    {
        $this->expectException(PathOutsideRoot::class);

        $this->createFilesystem()->copy('../../../some-file.txt', 'new-file.txt');
    }

    /**
     * @test
     */
    public function cannot_copy_to_destination_outside_of_root(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'content');

        $this->expectException(PathOutsideRoot::class);

        $filesystem->copy('file.txt', '../../../some-file.txt');
    }

    /**
     * @test
     */
    public function cannot_make_directory_outside_of_root(): void
    {
        $this->expectException(PathOutsideRoot::class);

        $this->createFilesystem()->mkdir('../../../some-dir');
    }

    /**
     * @test
     */
    public function cannot_check_existance_outside_of_root(): void
    {
        $this->expectException(PathOutsideRoot::class);

        $this->createFilesystem()->exists('../../../some-dir');
    }

    /**
     * @test
     */
    public function cannot_remove_file_outside_of_root(): void
    {
        $this->expectException(PathOutsideRoot::class);

        $this->createFilesystem()->delete('../../../some-dir');
    }

    /**
     * @test
     */
    public function cannot_write_outside_of_root(): void
    {
        $this->expectException(PathOutsideRoot::class);

        $this->createFilesystem()->write('../../../some-file.txt', 'contents');
    }

    /**
     * @test
     */
    public function cannot_move_source_outside_of_root(): void
    {
        $this->expectException(PathOutsideRoot::class);

        $this->createFilesystem()->move('../../../some-file.txt', 'new-file.txt');
    }

    /**
     * @test
     */
    public function cannot_move_destination_outside_of_root(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file.txt', 'content');

        $this->expectException(PathOutsideRoot::class);

        $filesystem->move('file.txt', '../../../some-file.txt');
    }

    abstract protected function createFilesystem(): Filesystem;
}
