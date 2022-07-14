<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use Zenstruck\Dsn\Group;
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
        if (!$dsn instanceof Group || !$dsn->scheme()->equals('url-prefix')) {
            throw new UnableToParseDsn();
        }

        $prefixes = $dsn->children();
        $inner = $prefixes[0];

        \array_shift($prefixes);

        return new UrlPrefixAdapter($this->factory()->create($inner), ...$prefixes);
    }
}
