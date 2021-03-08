<?php

namespace Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer;

use Zenstruck\Collection\Doctrine\ORM\Specification\ORMContext;
use Zenstruck\Collection\Specification\Normalizer\SplitSupports;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait ORMNormalizer
{
    use SplitSupports;

    protected function supportsContext($context): bool
    {
        return $context instanceof ORMContext;
    }
}
