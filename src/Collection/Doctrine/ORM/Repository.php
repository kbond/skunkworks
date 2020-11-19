<?php

namespace Zenstruck\Collection\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Repository implements \IteratorAggregate, \Countable
{
    private ?EntityRepository $repo = null;

    final public function getIterator(): \Traversable
    {
        return static::createResult($this->qb());
    }

    final public function batch(int $chunkSize = 100): \Traversable
    {
        return static::createResult($this->qb())->batch($chunkSize);
    }

    final public function batchProcess(int $chunkSize = 100): \Traversable
    {
        return static::createResult($this->qb())->batchProcess($chunkSize);
    }

    final public function count(): int
    {
        return $this->repo()->count([]);
    }

    protected static function createResult(QueryBuilder $qb, bool $fetchCollection = true, ?bool $useOutputWalkers = null): Result
    {
        return new Result($qb, $fetchCollection, $useOutputWalkers);
    }

    final protected function repo(): EntityRepository
    {
        return $this->repo ??= new EntityRepository($this->em(), $this->em()->getClassMetadata(static::className()));
    }

    final protected function qb(string $alias = 'entity', ?string $indexBy = null): QueryBuilder
    {
        return $this->repo()->createQueryBuilder($alias, $indexBy);
    }

    abstract protected static function className(): string;

    abstract protected function em(): EntityManagerInterface;
}
