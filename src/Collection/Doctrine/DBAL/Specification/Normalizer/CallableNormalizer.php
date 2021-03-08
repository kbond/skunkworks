<?php

namespace Zenstruck\Collection\Doctrine\DBAL\Specification\Normalizer;

use Zenstruck\Collection\Doctrine\DBAL\Specification\DBALContext;
use Zenstruck\Collection\Specification\Normalizer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CallableNormalizer implements Normalizer
{
    use DBALNormalizer;

    /**
     * @param callable    $specification
     * @param DBALContext $context
     */
    public function normalize($specification, $context)
    {
        return $specification($context->qb());
    }

    protected function supportsSpecification($specification): bool
    {
        return \is_callable($specification);
    }
}
