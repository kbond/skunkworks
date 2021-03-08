<?php

namespace Zenstruck\Collection\Doctrine\DBAL\Specification;

use Doctrine\DBAL\Query\QueryBuilder;
use Zenstruck\Collection\Doctrine\DBAL\Specification\Normalizer\CallableNormalizer;
use Zenstruck\Collection\Doctrine\DBAL\Specification\Normalizer\ComparisonNormalizer;
use Zenstruck\Collection\Doctrine\DBAL\Specification\Normalizer\CompositeNormalizer;
use Zenstruck\Collection\Doctrine\DBAL\Specification\Normalizer\NullNormalizer;
use Zenstruck\Collection\Doctrine\DBAL\Specification\Normalizer\OrderByNormalizer;
use Zenstruck\Collection\Specification\Normalizer\NestedNormalizer;
use Zenstruck\Collection\Specification\SpecificationNormalizer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class DBALContext
{
    private static ?SpecificationNormalizer $defaultNormalizer = null;
    private QueryBuilder $qb;

    public function __construct(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    public function qb(): QueryBuilder
    {
        return $this->qb;
    }

    public static function defaultNormalizer(): SpecificationNormalizer
    {
        return self::$defaultNormalizer ??= new SpecificationNormalizer([
            new NestedNormalizer(),
            new CallableNormalizer(),
            new ComparisonNormalizer(),
            new CompositeNormalizer(),
            new NullNormalizer(),
            new OrderByNormalizer(),
        ]);
    }
}
