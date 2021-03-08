<?php

namespace Zenstruck\Collection\Doctrine\DBAL\Specification\Normalizer;

use Zenstruck\Collection\Doctrine\DBAL\Specification\DBALContext;
use Zenstruck\Collection\Specification\Normalizer\SplitSupports;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait DBALNormalizer
{
    use SplitSupports;

    protected function supportsContext($context): bool
    {
        return $context instanceof DBALContext;
    }
}
