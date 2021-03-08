<?php

namespace Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer;

use Zenstruck\Collection\Doctrine\ORM\Specification\ORMContext;
use Zenstruck\Collection\Specification\Filter\Comparison;
use Zenstruck\Collection\Specification\Filter\Equal;
use Zenstruck\Collection\Specification\Filter\GreaterThan;
use Zenstruck\Collection\Specification\Filter\GreaterThanOrEqual;
use Zenstruck\Collection\Specification\Filter\In;
use Zenstruck\Collection\Specification\Filter\LessThan;
use Zenstruck\Collection\Specification\Filter\LessThanOrEqual;
use Zenstruck\Collection\Specification\Filter\Like;
use Zenstruck\Collection\Specification\Filter\NotEqual;
use Zenstruck\Collection\Specification\Filter\NotIn;
use Zenstruck\Collection\Specification\Filter\NotLike;
use Zenstruck\Collection\Specification\Normalizer;
use Zenstruck\Collection\Specification\Normalizer\ClassMethodMap;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ComparisonNormalizer implements Normalizer
{
    use ClassMethodMap, ORMNormalizer;

    /**
     * @param Comparison $specification
     * @param ORMContext $context
     */
    public function normalize($specification, $context): string
    {
        $parameter = \sprintf('comparison_%d', $context->qb()->getParameters()->count());
        $context->qb()->setParameter($parameter, $specification->value());

        return $context->qb()->expr()->{self::methodFor($specification)}(
            "{$context->alias()}.{$specification->field()}",
            ":{$parameter}"
        );
    }

    protected static function classMethodMap(): array
    {
        return [
            Equal::class => 'eq',
            NotEqual::class => 'neq',
            GreaterThan::class => 'gt',
            GreaterThanOrEqual::class => 'gte',
            LessThan::class => 'lt',
            LessThanOrEqual::class => 'lte',
            Like::class => 'like',
            NotLike::class => 'notLike',
            In::class => 'in',
            NotIn::class => 'notIn',
        ];
    }
}
