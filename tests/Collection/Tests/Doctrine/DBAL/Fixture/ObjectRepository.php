<?php

namespace Zenstruck\Collection\Tests\Doctrine\DBAL\Fixture;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Zenstruck\Collection;
use Zenstruck\Collection\Doctrine\DBAL\ObjectRepository as BaseObjectRepository;
use Zenstruck\Collection\Doctrine\DBAL\ObjectResult;
use Zenstruck\Collection\Paginatable;
use Zenstruck\Collection\Tests\Doctrine\Fixture\Entity;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ObjectRepository extends BaseObjectRepository implements Collection
{
    use Paginatable;

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function take(int $limit, int $offset = 0): Collection
    {
        return static::createResult($this->qb())->take($limit, $offset);
    }

    protected static function createResult(QueryBuilder $qb): ObjectResult
    {
        return new ObjectResult(fn(array $data) => static::createObject($data), $qb);
    }

    protected static function createObject(array $data): Entity
    {
        return new Entity($data['value'], $data['id']);
    }

    protected static function tableName(): string
    {
        return Entity::TABLE;
    }

    protected function connection(): Connection
    {
        return $this->connection;
    }
}
