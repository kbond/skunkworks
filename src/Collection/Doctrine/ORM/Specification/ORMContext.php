<?php

namespace Zenstruck\Collection\Doctrine\ORM\Specification;

use Doctrine\ORM\QueryBuilder;
use Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer\CallableNormalizer;
use Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer\ComparisonNormalizer;
use Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer\CompositeNormalizer;
use Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer\NullNormalizer;
use Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer\OrderByNormalizer;
use Zenstruck\Collection\Specification\Normalizer\NestedNormalizer;
use Zenstruck\Collection\Specification\SpecificationNormalizer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ORMContext
{
    private static ?SpecificationNormalizer $defaultNormalizer = null;
    private QueryBuilder $qb;
    private string $alias;

    public function __construct(QueryBuilder $qb, string $alias)
    {
        $this->qb = $qb;
        $this->alias = $alias;
    }

    public function qb(): QueryBuilder
    {
        return $this->qb;
    }

    public function alias(): string
    {
        return $this->alias;
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
