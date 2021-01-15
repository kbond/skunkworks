<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Adapter\HttpClientAdapter;
use Zenstruck\Filesystem\AdapterFilesystem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class HttpClientAdapterTest extends TestCase
{
    /**
     * @test
     */
    public function can_access_file(): void
    {
        $filesystem = $this->createFilesystem('https://example.com', $this->mockClient(new MockResponse(
            'content',
            ['http_code' => 200]
        )));

        $file = $filesystem->file('/foo/bar.txt');

        $this->assertSame('content', $file->contents());
        $this->assertSame('content', \stream_get_contents($file->read()));
        $this->assertSame('https://example.com/foo/bar.txt', $file->url()->toString());
    }

    /**
     * @test
     */
    public function check_for_existence(): void
    {
        $filesystem = $this->createFilesystem('https://example.com', $this->mockClient(
            new MockResponse(
                'content',
                ['http_code' => 404]
            ),
            new MockResponse(
                'content',
                ['http_code' => 200]
            ),
        ));

        $this->assertFalse($filesystem->exists('foo'));
        $this->assertTrue($filesystem->exists('bar'));
    }

    /**
     * @test
     */
    public function can_get_file_size_from_content_length_header(): void
    {
        $filesystem = $this->createFilesystem('https://example.com', $this->mockClient(new MockResponse(
            'content',
            [
                'http_code' => 200,
                'response_headers' => ['Content-Length' => 7],
            ]
        )));

        $this->assertSame(7, $filesystem->file('/foo/bar')->size());
    }

    /**
     * @test
     * @dataProvider contentTypeProvider
     */
    public function can_get_mime_type_from_content_type_header($contentType, $expectedMimeType): void
    {
        $filesystem = $this->createFilesystem('https://example.com', $this->mockClient(new MockResponse(
            'content',
            [
                'http_code' => 200,
                'response_headers' => ['Content-Type' => $contentType],
            ]
        )));

        $this->assertSame($expectedMimeType, $filesystem->file('/foo/bar')->mimeType());
    }

    /**
     * @test
     */
    public function can_get_last_modified_from_date_header(): void
    {
        $filesystem = $this->createFilesystem('https://example.com', $this->mockClient(new MockResponse(
            'content',
            [
                'http_code' => 200,
                'response_headers' => ['Date' => 'Wed, 21 Oct 2015 07:28:00 GMT'],
            ]
        )));

        $this->assertSame('2015-10-21', $filesystem->file('/foo/bar')->lastModified()->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function can_get_last_modified_from_last_modified_header(): void
    {
        $filesystem = $this->createFilesystem('https://example.com', $this->mockClient(new MockResponse(
            'content',
            [
                'http_code' => 200,
                'response_headers' => ['Last-Modified' => 'Wed, 21 Oct 2015 07:28:00 GMT'],
            ]
        )));

        $this->assertSame('2015-10-21', $filesystem->file('/foo/bar')->lastModified()->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function last_modified_uses_last_modified_header_if_date_header_exists(): void
    {
        $filesystem = $this->createFilesystem('https://example.com', $this->mockClient(new MockResponse(
            'content',
            [
                'http_code' => 200,
                'response_headers' => [
                    'Last-Modified' => 'Wed, 21 Oct 2015 07:28:00 GMT',
                    'Date' => 'Sun, 02 Feb 2020 07:28:00 GMT',
                ],
            ]
        )));

        $this->assertSame('2015-10-21', $filesystem->file('/foo/bar')->lastModified()->format('Y-m-d'));
    }

    public static function contentTypeProvider(): iterable
    {
        yield ['application/pdf', 'application/pdf'];
        yield ['application/pdf ; foo=bar; baz=foo', 'application/pdf'];
    }

    private function createFilesystem(string $url, HttpClientInterface $client): Filesystem
    {
        return new AdapterFilesystem(new HttpClientAdapter($url, $client));
    }

    private function mockClient(MockResponse ...$responses): MockHttpClient
    {
        return new MockHttpClient($responses);
    }
}
