<?php

namespace Zenstruck\Filesystem\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Filesystem\TempFile;
use Zenstruck\Filesystem\Util\ResourceWrapper;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TempFileTest extends TestCase
{
    /**
     * @test
     */
    public function can_create_for_existing_file(): void
    {
        $file = new TempFile(\sys_get_temp_dir().'/zs'.__METHOD__);

        $this->assertFileDoesNotExist($file);

        \file_put_contents($file, 'contents');

        $this->assertFileExists($file);
    }

    /**
     * @test
     */
    public function exists_when_created(): void
    {
        $this->assertFileExists(new TempFile());
    }

    /**
     * @test
     */
    public function can_delete(): void
    {
        \file_put_contents($file = new TempFile(), 'contents');

        $this->assertFileExists($file);

        $file->delete();
        $file->delete();

        $this->assertFileDoesNotExist($file);
    }

    /**
     * @test
     */
    public function cannot_create_for_directory(): void
    {
        $this->expectException(\LogicException::class);

        new TempFile(__DIR__);
    }

    /**
     * @test
     */
    public function can_create_for_stream(): void
    {
        $file = TempFile::forStream(ResourceWrapper::inMemory()->write('file contents')->rewind()->get());

        $this->assertFileExists($file);
        $this->assertStringEqualsFile($file, 'file contents');
    }

    /**
     * @test
     */
    public function can_use_tap(): void
    {
        $file = TempFile::tap(function(\SplFileInfo $file) { \file_put_contents($file, 'file contents'); });

        $this->assertFileExists($file);
        $this->assertStringEqualsFile($file, 'file contents');
    }
}
