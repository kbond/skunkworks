<?php

namespace Zenstruck\Collection\Doctrine\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Repository implements \IteratorAggregate, \Countable
{
    public function getIterator(): \Traversable
    {
        return static::createResult($this->qb());
    }

    public function count(): int
    {
        return static::createResult($this->qb())->count();
    }

    protected static function createResult(QueryBuilder $qb): Result
    {
        return new Result($qb);
    }

    final protected function qb(): QueryBuilder
    {
        return $this->connection()->createQueryBuilder()->select('*')->from(static::tableName());
    }

    abstract protected static function tableName(): string;

    abstract protected function connection(): Connection;
}