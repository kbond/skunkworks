<?php

namespace Zenstruck\Filesystem;

use Zenstruck\Dsn;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Adapter\Factory\AdapterFactory;

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
}
