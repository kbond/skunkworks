<?php

namespace Zenstruck\Filesystem\Adapter;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\StreamWrapper;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Exception\NotFound;
use Zenstruck\Filesystem\Feature\AccessUrl;
use Zenstruck\Filesystem\Util\ResourceWrapper;
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class HttpClientAdapter implements Adapter, AccessUrl
{
    private Url $url;
    private HttpClientInterface $client;
    private array $responseCache = [];

    /**
     * @param string|Url $url
     */
    public function __construct($url, ?HttpClientInterface $client = null)
    {
        $this->url = Url::create($url);
        $this->client = $client ?? HttpClient::create();
    }

    public function url(string $path): Url
    {
        return $this->url->appendPath($path);
    }

    public function type(string $path): string
    {
        $this->fetch($path);

        return self::TYPE_FILE;
    }

    public function read(string $path)
    {
        $response = $this->fetch($path);

        if (\is_callable([$response, 'toStream'])) {
            $stream = $response->toStream();
        } else {
            $stream = StreamWrapper::createResource($response, $this->client);
        }

        return ResourceWrapper::wrap($stream)->rewind()->get();
    }

    public function contents(string $path): string
    {
        return $this->fetch($path)->getContent();
    }

    public function modifiedAt(string $path): int
    {
        $headers = $this->fetch($path)->getHeaders();

        if (null === $lastModified = $headers['last-modified'][0] ?? $headers['date'][0] ?? null) {
            throw new \RuntimeException('Response does not have "Last-Modified" or "Date" header.');
        }

        return (int) \is_numeric($lastModified) ? $lastModified : (new \DateTime($lastModified))->getTimestamp();
    }

    public function mimeType(string $path): string
    {
        if (null === $contentType = $this->fetch($path)->getHeaders()['content-type'][0] ?? null) {
            throw new \RuntimeException('Response does not have "Content-Type" header.');
        }

        // might be "text/plain; charset=utf-8"
        return \trim(\explode(';', $contentType)[0]);
    }

    public function size(string $path): int
    {
        if (null === $size = $this->fetch($path)->getHeaders()['content-length'][0] ?? null) {
            throw new \RuntimeException('Response does not have "Content-Length" header.');
        }

        return $size;
    }

    private function fetch(string $path): ResponseInterface
    {
        if (isset($this->responseCache[$path])) {
            return $this->responseCache[$path];
        }

        $response = $this->client->request('GET', $this->url($path));

        try {
            $response->getHeaders(true);
        } catch (ClientExceptionInterface $e) {
            if (404 === $e->getResponse()->getStatusCode()) {
                throw NotFound::forPath($path);
            }

            throw $e;
        }

        return $this->responseCache[$path] = $response;
    }
}
