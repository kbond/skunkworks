<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use Zenstruck\Dsn\Decorated;
use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\Factory;
use Zenstruck\Filesystem\Adapter\UrlPrefixAdapter;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlPrefixAdapterFactory implements Factory, FactoryAware
{
    use ProvideFactory;

    public function create(\Stringable $dsn): Adapter
    {
        if (!$dsn instanceof Decorated || !$dsn->scheme()->equals('url-prefix')) {
            throw new UnableToParseDsn();
        }

        if (!$dsn->query()->has('prefix')) {
            throw new \LogicException('url-prefix DSN must have a prefix parameter.');
        }

        return new UrlPrefixAdapter(
            $this->factory()->create($dsn->inner()),
            ...(array) $dsn->query()->get('prefix')
        );
    }
}
