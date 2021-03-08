<?php

namespace Zenstruck\Collection\Doctrine\DBAL\Specification\Normalizer;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Zenstruck\Collection\Doctrine\DBAL\Specification\DBALContext;
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
    use ClassMethodMap, DBALNormalizer, HasNormalizer;

    /**
     * @param Composite   $specification
     * @param DBALContext $context
     */
    public function normalize($specification, $context): ?CompositeExpression
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
