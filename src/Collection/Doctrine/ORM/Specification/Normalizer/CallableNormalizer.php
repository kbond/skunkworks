<?php

namespace Zenstruck\Collection\Doctrine\ORM\Specification\Normalizer;

use Zenstruck\Collection\Doctrine\ORM\Specification\ORMContext;
use Zenstruck\Collection\Specification\Normalizer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CallableNormalizer implements Normalizer
{
    use ORMNormalizer;

    /**
     * @param callable   $specification
     * @param ORMContext $context
     */
    public function normalize($specification, $context)
    {
        return $specification($context->qb(), $context->alias());
    }

    protected function supportsSpecification($specification): bool
    {
        return \is_callable($specification);
    }
}
