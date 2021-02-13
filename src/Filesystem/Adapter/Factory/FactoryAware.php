<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use Zenstruck\Filesystem\Adapter\Factory;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface FactoryAware
{
    public function setFactory(Factory $factory);
}
