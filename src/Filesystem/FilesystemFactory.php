<?php

namespace Zenstruck\Filesystem;

use Zenstruck\Dsn;
use Zenstruck\Dsn\Decorated;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Adapter\Factory\AdapterFactory;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;
use Zenstruck\Filesystem\Node\File;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FilesystemFactory
{
    private AdapterFactory $adapterFactory;

    public function __construct(?AdapterFactory $adapterFactory = null)
    {
        $this->adapterFactory = $adapterFactory ?? new AdapterFactory();
    }

    public function create(string $dsn): Filesystem
    {
        return new AdapterFilesystem($this->adapterFactory->create(Dsn::parse($dsn)));
    }

    public function file(string $dsn): File
    {
        $parsed = Dsn::parse($dsn);

        if (!$parsed instanceof Decorated || !$parsed->scheme()->equals('file')) {
            throw new UnableToParseDsn('A "file()" DSN is required (ie "file({filesystem})?path=some/path.txt").');
        }

        return $this
            ->create($parsed->inner())
            ->file($parsed->query()->get('path', new \LogicException('A "path" parameter is required.')))
        ;
    }
}
