<?php

namespace Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer;

use Zenstruck\Collection\Doctrine\ORM\Specification\ORMContext;
use Zenstruck\Collection\Specification\Field;
use Zenstruck\Collection\Specification\Filter\IsNotNull;
use Zenstruck\Collection\Specification\Filter\IsNull;
use Zenstruck\Collection\Specification\Normalizer;
use Zenstruck\Collection\Specification\Normalizer\ClassMethodMap;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class NullNormalizer implements Normalizer
{
    use ClassMethodMap, ORMNormalizer;

    /**
     * @param Field      $specification
     * @param ORMContext $context
     */
    public function normalize($specification, $context): string
    {
        return $context->qb()->expr()->{self::methodFor($specification)}("{$context->alias()}.{$specification->field()}");
    }

    protected static function classMethodMap(): array
    {
        return [
            IsNull::class => 'isNull',
            IsNotNull::class => 'isNotNull',
        ];
    }
}
