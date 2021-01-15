<?php

namespace Zenstruck\Filesystem\Test;

use Zenstruck\Filesystem\Adapter\StaticInMemoryAdapter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait ResetStaticInMemoryAdapter
{
    /**
     * @internal
     * @before
     */
    final public static function _resetStaticInMemoryAdapter(): void
    {
        StaticInMemoryAdapter::reset();
    }
}
