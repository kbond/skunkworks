<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use League\Flysystem\Filesystem;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\Factory;
use Zenstruck\Filesystem\Adapter\FlysystemAdapter;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;
use Zenstruck\Uri;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FlysystemFtpAdapterFactory implements Factory
{
    public function create(\Stringable $dsn): Adapter
    {
        if (!$dsn instanceof Uri || !$dsn->scheme()->equals('flysystem+ftp')) {
            throw new UnableToParseDsn();
        }

        return new FlysystemAdapter(new Filesystem(
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
}
