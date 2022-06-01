<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\Factory;
use Zenstruck\Filesystem\Adapter\StreamAdapter;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;
use Zenstruck\Uri;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class StreamAdapterFactory implements Factory
{
    public function create(\Stringable $dsn): Adapter
    {
        if (!$dsn instanceof Uri || !$dsn->scheme()->in(\array_merge(\stream_get_wrappers(), ['']))) {
            throw new UnableToParseDsn();
        }

        return new StreamAdapter($dsn->withoutQuery()->withoutFragment());
    }
}
