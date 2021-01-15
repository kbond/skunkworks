<?php

namespace Zenstruck\Filesystem\Tests\Bridge\HttpFoundation;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Filesystem\Adapter\InMemoryAdapter;
use Zenstruck\Filesystem\Adapter\StreamAdapter;
use Zenstruck\Filesystem\Adapter\TempFileAdapter;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\Bridge\HttpFoundation\ResponseFactory;
use Zenstruck\Filesystem\Bridge\HttpFoundation\StreamedFileResponse;
use Zenstruck\Filesystem\Node\File;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ResponseFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider fileProvider
     */
    public function can_create(File $file, string $expectedResponseClass): void
    {
        \ob_start();
        $response = ResponseFactory::create($file)->prepare(Request::create(''))->send();
        $output = \ob_get_clean();

        $this->assertInstanceOf($expectedResponseClass, $response);
        $this->assertTrue($response->headers->has('last-modified'));
        $this->assertSame($file->lastModified()->format('Y-m-d O'), (new \DateTime($response->headers->get('last-modified')))->format('Y-m-d O'));
        $this->assertTrue($response->headers->has('content-type'));
        $this->assertStringContainsString($file->mimeType(), $response->headers->get('content-type'));
        $this->assertFalse($response->headers->has('content-disposition'));
        $this->assertSame($file->contents(), $output);
    }

    /**
     * @test
     * @dataProvider fileProvider
     */
    public function can_create_as_inline(File $file, string $expectedResponseClass): void
    {
        \ob_start();
        $response = ResponseFactory::inline($file)->prepare(Request::create(''))->send();
        $output = \ob_get_clean();

        $this->assertInstanceOf($expectedResponseClass, $response);
        $this->assertTrue($response->headers->has('last-modified'));
        $this->assertSame($file->lastModified()->format('Y-m-d O'), (new \DateTime($response->headers->get('last-modified')))->format('Y-m-d O'));
        $this->assertTrue($response->headers->has('content-type'));
        $this->assertStringContainsString($file->mimeType(), $response->headers->get('content-type'));
        $this->assertTrue($response->headers->has('content-disposition'));
        $this->assertSame("inline; filename={$file->filename()}", $response->headers->get('content-disposition'));
        $this->assertSame($file->contents(), $output);
    }

    /**
     * @test
     * @dataProvider fileProvider
     */
    public function can_create_as_attachment(File $file, string $expectedResponseClass): void
    {
        \ob_start();
        $response = ResponseFactory::attachment($file)->prepare(Request::create(''))->send();
        $output = \ob_get_clean();

        $this->assertInstanceOf($expectedResponseClass, $response);
        $this->assertTrue($response->headers->has('last-modified'));
        $this->assertSame($file->lastModified()->format('Y-m-d O'), (new \DateTime($response->headers->get('last-modified')))->format('Y-m-d O'));
        $this->assertTrue($response->headers->has('content-type'));
        $this->assertStringContainsString($file->mimeType(), $response->headers->get('content-type'));
        $this->assertTrue($response->headers->has('content-disposition'));
        $this->assertSame("attachment; filename={$file->filename()}", $response->headers->get('content-disposition'));
        $this->assertSame($file->contents(), $output);
    }

    public static function fileProvider(): iterable
    {
        yield [File::create(new AdapterFilesystem(new InMemoryAdapter()), 'some/file.txt', 'content'), StreamedFileResponse::class];
        yield [(new AdapterFilesystem(new StreamAdapter(__DIR__.'/../../Fixture/directory')))->file('nested/file2.txt'), BinaryFileResponse::class];
        yield [File::create(new AdapterFilesystem(new TempFileAdapter(new InMemoryAdapter())), 'some/file.txt', 'content'), BinaryFileResponse::class];
    }
}
