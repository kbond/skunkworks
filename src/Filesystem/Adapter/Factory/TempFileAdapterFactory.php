<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use Zenstruck\Dsn\Decorated;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\Factory;
use Zenstruck\Filesystem\Adapter\TempFileAdapter;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TempFileAdapterFactory implements Factory, FactoryAware
{
    use ProvideFactory;

    public function create(\Stringable $dsn): Adapter
    {
        if (!$dsn instanceof Decorated || !$dsn->scheme()->equals('temp-file')) {
            throw new UnableToParseDsn();
        }

        return new TempFileAdapter($this->factory()->create($dsn->inner()));
    }
}
