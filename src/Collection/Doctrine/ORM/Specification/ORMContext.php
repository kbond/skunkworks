<?php

namespace Zenstruck\Collection\Doctrine\ORM\Specification;

use Doctrine\ORM\QueryBuilder;
use Zenstruck\Collection\Doctrine\Specification\Context;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ORMContext extends Context
{
    private QueryBuilder $qb;

    public function __construct(QueryBuilder $qb, string $alias)
    {
        parent::__construct($alias);

        $this->qb = $qb;
    }

    public function qb(): QueryBuilder
    {
        return $this->qb;
    }
}
