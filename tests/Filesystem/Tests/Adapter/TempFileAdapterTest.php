<?php

namespace Zenstruck\Filesystem\Tests\Adapter;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Adapter\InMemoryAdapter;
use Zenstruck\Filesystem\Adapter\TempFileAdapter;
use Zenstruck\Filesystem\Tests\Feature\AccessRealFileTests;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TempFileAdapterTest extends AdapterTest
{
    use AccessRealFileTests;

    protected function createAdapter(): Adapter
    {
        return new TempFileAdapter(new InMemoryAdapter());
    }
}
