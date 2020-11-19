<?php

namespace Zenstruck\Collection\Doctrine\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Zenstruck\Collection;
use Zenstruck\Collection\FactoryCollection;
use Zenstruck\Collection\IterableCollection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ObjectResult extends Result
{
    private $factory;

    public function __construct(callable $factory, QueryBuilder $qb, ?callable $countModifier = null)
    {
        $this->factory = $factory;

        parent::__construct($qb, $countModifier);
    }

    public function take(int $limit, int $offset = 0): Collection
    {
        return new FactoryCollection(parent::take($limit, $offset), $this->factory);
    }

    public function getIterator(): \Traversable
    {
        return new FactoryCollection(new IterableCollection(fn() => parent::getIterator()), $this->factory);
    }
}
