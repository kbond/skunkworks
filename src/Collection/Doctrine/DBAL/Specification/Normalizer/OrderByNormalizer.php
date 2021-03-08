<?php

namespace Zenstruck\Collection\Doctrine\DBAL\Specification\Normalizer;

use Zenstruck\Collection\Doctrine\DBAL\Specification\DBALContext;
use Zenstruck\Collection\Specification\Normalizer;
use Zenstruck\Collection\Specification\OrderBy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class OrderByNormalizer implements Normalizer
{
    use DBALNormalizer;

    /**
     * @param OrderBy     $specification
     * @param DBALContext $context
     */
    public function normalize($specification, $context): void
    {
        $context->qb()->addOrderBy($specification->field(), $specification->direction());
    }

    protected function supportsSpecification($specification): bool
    {
        return $specification instanceof OrderBy;
    }
}
