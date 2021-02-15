<?php

namespace Zenstruck\Filesystem\Tests\Feature;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Exception\RuntimeException;
use Zenstruck\Filesystem\Util\ResourceWrapper;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait WriteFileTests
{
    /**
     * @test
     */
    public function can_write_string_contents(): void
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write('file.txt', 'contents');

        $this->assertSame('contents', $filesystem->file('file.txt')->contents());
    }

    /**
     * @test
     */
    public function can_write_string_contents_to_existing_file(): void
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write('file.txt', 'contents');
        $filesystem->write('file.txt', 'contents2');

        $this->assertSame('contents2', $filesystem->file('file.txt')->contents());
    }

    /**
     * @test
     */
    public function cannot_write_string_to_existing_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->mkdir('dir');

        try {
            $filesystem->write('dir', 'contents');
        } catch (RuntimeException $e) {
            $this->assertTrue($filesystem->node('dir')->isDirectory());

            return;
        }

        $this->fail('Exception not thrown.');
    }

    /**
     * @test
     */
    public function can_write_file(): void
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write('file.txt', __FILE__);

        $this->assertStringContainsString('<?php', $filesystem->file('file.txt')->contents());
    }

    /**
     * @test
     */
    public function can_write_file_to_existing_file(): void
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write('file.txt', 'contents');

        $this->assertStringNotContainsString('<?php', $filesystem->file('file.txt')->contents());

        $filesystem->write('file.txt', __FILE__);

        $this->assertStringContainsString('<?php', $filesystem->file('file.txt')->contents());
    }

    /**
     * @test
     */
    public function cannot_write_file_to_existing_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->mkdir('dir');

        try {
            $filesystem->write('dir', __FILE__);
        } catch (RuntimeException $e) {
            $this->assertTrue($filesystem->node('dir')->isDirectory());

            return;
        }

        $this->fail('Exception not thrown.');
    }

    /**
     * @test
     */
    public function cannot_write_file_that_is_directory(): void
    {
        $filesystem = $this->createFilesystem();

        try {
            $filesystem->write('dir', __DIR__);
        } catch (RuntimeException $e) {
            $this->assertFalse($filesystem->exists('dir'));

            return;
        }

        $this->fail('Exception not thrown.');
    }

    /**
     * @test
     */
    public function can_write_resource(): void
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write('file.txt', ResourceWrapper::inMemory()->write('contents')->rewind()->get());

        $this->assertSame('contents', $filesystem->file('file.txt')->contents());
    }

    /**
     * @test
     */
    public function can_write_resource_to_existing_file(): void
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write('file.txt', 'contents');
        $filesystem->write('file.txt', ResourceWrapper::inMemory()->write('contents2')->rewind()->get());

        $this->assertSame('contents2', $filesystem->file('file.txt')->contents());
    }

    /**
     * @test
     */
    public function cannot_write_resource_to_existing_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->mkdir('dir');

        try {
            $filesystem->write('dir', ResourceWrapper::inMemory()->write('contents')->rewind()->get());
        } catch (RuntimeException $e) {
            $this->assertTrue($filesystem->node('dir')->isDirectory());

            return;
        }

        $this->fail('Exception not thrown.');
    }

    /**
     * @test
     */
    public function can_write_filesystem_file(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('file2.txt', 'contents');
        $filesystem->write('file.txt', $filesystem->file('file2.txt'));

        $this->assertSame('contents', $filesystem->file('file.txt')->contents());
    }

    /**
     * @test
     */
    public function can_write_filesystem_file_to_existing_file(): void
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write('file.txt', 'contents');
        $filesystem->write('file2.txt', 'contents2');
        $filesystem->write('file.txt', $filesystem->file('file2.txt'));

        $this->assertSame('contents2', $filesystem->file('file.txt')->contents());
    }

    /**
     * @test
     */
    public function cannot_write_filesystem_file_to_existing_directory(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->mkdir('dir');
        $filesystem->write('file.txt', 'contents');

        try {
            $filesystem->write('dir', $filesystem->file('file.txt'));
        } catch (RuntimeException $e) {
            $this->assertTrue($filesystem->node('dir')->isDirectory());

            return;
        }

        $this->fail('Exception not thrown.');
    }

    abstract protected function createFilesystem(): Filesystem;
}
