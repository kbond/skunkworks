<?php

namespace Zenstruck\Filesystem;

use PHPUnit\Framework\Assert as PHPUnit;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Node\Directory;
use Zenstruck\Filesystem\Node\File;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TestFilesystem implements Filesystem
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function assertExists(string $path): self
    {
        PHPUnit::assertTrue($this->exists($path));

        return $this;
    }

    public function assertNotExists(string $path): self
    {
        PHPUnit::assertFalse($this->exists($path));

        return $this;
    }

    public function assertContents(string $path, string $expected): self
    {
        PHPUnit::assertSame($expected, $this->file($path)->contents());

        return $this;
    }

    public function assertNotContents(string $path, string $expected): self
    {
        PHPUnit::assertNotSame($expected, $this->file($path)->contents());

        return $this;
    }

    public function assertContentsContains(string $path, string $expected): self
    {
        PHPUnit::assertStringContainsString($expected, $this->file($path)->contents());

        return $this;
    }

    public function assertContentsNotContains(string $path, string $expected): self
    {
        PHPUnit::assertStringNotContainsString($expected, $this->file($path)->contents());

        return $this;
    }

    public function assertSame(string $path1, string $path2): self
    {
        PHPUnit::assertSame($this->file($path1)->contents(), $this->file($path2)->contents());

        return $this;
    }

    public function assertNotSame(string $path1, string $path2): self
    {
        PHPUnit::assertNotSame($this->file($path1)->contents(), $this->file($path2)->contents());

        return $this;
    }

    public function node(string $path = ''): Node
    {
        return $this->filesystem->node($path);
    }

    public function file(string $path): File
    {
        return $this->filesystem->file($path);
    }

    public function directory(string $path = ''): Directory
    {
        return $this->filesystem->directory($path);
    }

    public function exists(string $path = ''): bool
    {
        return $this->filesystem->exists($path);
    }

    public function copy(string $source, string $destination): void
    {
        $this->filesystem->copy($source, $destination);
    }

    public function move(string $source, string $destination): void
    {
        $this->filesystem->move($source, $destination);
    }

    public function delete(string $path = ''): void
    {
        $this->filesystem->delete($path);
    }

    public function mkdir(string $path = ''): void
    {
        $this->filesystem->mkdir($path);
    }

    public function write(string $path, $value): void
    {
        $this->filesystem->write($path, $value);
    }

    public function supports(string $feature): bool
    {
        return $this->filesystem->supports($feature);
    }
}
