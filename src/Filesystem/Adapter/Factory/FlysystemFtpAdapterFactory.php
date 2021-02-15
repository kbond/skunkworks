<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Filesystem;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\Factory;
use Zenstruck\Filesystem\Adapter\FlysystemV1Adapter;
use Zenstruck\Filesystem\Adapter\FlysystemV2Adapter;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;
use Zenstruck\Url;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FlysystemFtpAdapterFactory implements Factory
{
    public function create(\Stringable $dsn): Adapter
    {
        if (!$dsn instanceof Url || !$dsn->scheme()->equals('flysystem+ftp')) {
            throw new UnableToParseDsn();
        }

        if (\class_exists(Ftp::class)) {
            return $this->createV1Adapter($dsn);
        }

        return new FlysystemV2Adapter(new Filesystem(
            new FtpAdapter(FtpConnectionOptions::fromArray([
                'host' => $dsn->host()->toString(),
                'username' => $dsn->user(),
                'password' => $dsn->pass(),
                'port' => $dsn->port() ?? 21,
                'root' => $dsn->path()->absolute(),
                'ssl' => $dsn->query()->getBool('ssl'),
            ]))
        ));
    }

    private function createV1Adapter(Url $dsn): Adapter
    {
        return new FlysystemV1Adapter(new Filesystem(
            new Ftp(\array_filter([
                'host' => $dsn->host()->toString(),
                'username' => $dsn->user(),
                'password' => $dsn->pass(),
                'port' => $dsn->port() ?? 21,
                'root' => $dsn->path()->absolute(),
                'ssl' => $dsn->query()->getBool('ssl'),
            ]))
        ));
    }
}
