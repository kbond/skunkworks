<?php

namespace Zenstruck\Filesystem\Adapter;

use Zenstruck\Filesystem\Adapter;
use Zenstruck\Filesystem\Exception\UnableToParseDsn;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Factory
{
    /**
     * @throws UnableToParseDsn
     */
    public function create(\Stringable $dsn): Adapter;
}
