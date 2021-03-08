<?php

namespace Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer;

use Doctrine\ORM\Query\Expr;
use Zenstruck\Collection\Doctrine\ORM\Specification\ORMContext;
use Zenstruck\Collection\Specification\Logic\AndX;
use Zenstruck\Collection\Specification\Logic\Composite;
use Zenstruck\Collection\Specification\Logic\OrX;
use Zenstruck\Collection\Specification\Normalizer;
use Zenstruck\Collection\Specification\Normalizer\ClassMethodMap;
use Zenstruck\Collection\Specification\Normalizer\HasNormalizer;
use Zenstruck\Collection\Specification\Normalizer\NormalizerAware;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CompositeNormalizer implements Normalizer, NormalizerAware
{
    use ClassMethodMap, HasNormalizer, ORMNormalizer;

    /**
     * @param Composite  $specification
     * @param ORMContext $context
     */
    public function normalize($specification, $context): ?Expr\Composite
    {
        $children = \array_filter(\array_map(
            function($child) use ($context) {
                return $this->normalizer()->normalize($child, $context);
            },
            $specification->children()
        ));

        if (empty($children)) {
            return null;
        }

        return $context->qb()->expr()->{self::methodFor($specification)}(...$children);
    }

    protected static function classMethodMap(): array
    {
        return [
            AndX::class => 'andX',
            OrX::class => 'orX',
        ];
    }
}
