<?php

namespace Zenstruck\Filesystem\Tests\Node;

use PHPUnit\Framework\TestCase;
use Zenstruck\Filesystem\FilesystemFactory;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Filesystem\Test\ResetStaticInMemoryAdapter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FileTest extends TestCase
{
    use ResetStaticInMemoryAdapter;

    /**
     * @test
     */
    public function can_create_for_dsn(): void
    {
        $filesystem = (new FilesystemFactory())->create('in-memory:?static=true');
        $filesystem->write('nested/file.txt', 'content');

        $file = File::forDsn('file(in-memory:?static=true)?path=nested/file.txt');

        $this->assertSame('content', $file->contents());
    }
}
