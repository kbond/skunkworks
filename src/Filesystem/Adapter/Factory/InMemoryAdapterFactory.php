<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\Factory;
use Zenstruck\Filesystem\Adapter\InMemoryAdapter;
use Zenstruck\Filesystem\Adapter\StaticInMemoryAdapter;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;
use Zenstruck\Uri;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class InMemoryAdapterFactory implements Factory
{
    public function create(\Stringable $dsn): Adapter
    {
        if (!$dsn instanceof Uri || !$dsn->scheme()->equals('in-memory')) {
            throw new UnableToParseDsn();
        }

        if ($dsn->query()->getBool('static')) {
            return new StaticInMemoryAdapter($dsn->query()->get('name'));
        }

        return new InMemoryAdapter();
    }
}
