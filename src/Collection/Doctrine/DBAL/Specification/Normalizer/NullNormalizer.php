<?php

namespace Zenstruck\Collection\Doctrine\DBAL\Specification\Normalizer;

use Zenstruck\Collection\Doctrine\DBAL\Specification\DBALContext;
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
    use ClassMethodMap, DBALNormalizer;

    /**
     * @param Field       $specification
     * @param DBALContext $context
     */
    public function normalize($specification, $context): string
    {
        return $context->qb()->expr()->{self::methodFor($specification)}($specification->field());
    }

    protected static function classMethodMap(): array
    {
        return [
            IsNull::class => 'isNull',
            IsNotNull::class => 'isNotNull',
        ];
    }
}
