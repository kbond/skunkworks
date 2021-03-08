<?php

namespace Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer;

use Zenstruck\Collection\Doctrine\ORM\Specification\ORMContext;
use Zenstruck\Collection\Specification\Normalizer;
use Zenstruck\Collection\Specification\OrderBy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class OrderByNormalizer implements Normalizer
{
    use ORMNormalizer;

    /**
     * @param OrderBy    $specification
     * @param ORMContext $context
     */
    public function normalize($specification, $context): void
    {
        $context->qb()->addOrderBy("{$context->alias()}.{$specification->field()}", $specification->direction());
    }

    protected function supportsSpecification($specification): bool
    {
        return $specification instanceof OrderBy;
    }
}
