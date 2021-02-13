<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\Factory;
use Zenstruck\Filesystem\Adapter\HttpClientAdapter;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class HttpClientAdapterFactory implements Factory
{
    private ?HttpClientInterface $client;

    public function __construct(?HttpClientInterface $client = null)
    {
        $this->client = $client;
    }

    public function create(\Stringable $dsn): Adapter
    {
        if (!$dsn instanceof Url || !$dsn->scheme()->in(['http', 'https'])) {
            throw new UnableToParseDsn();
        }

        return new HttpClientAdapter($dsn, $this->client);
    }
}
