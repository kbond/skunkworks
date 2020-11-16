<?php

namespace Zenstruck\Collection\Tests\Doctrine\ORM\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Collection;
use Zenstruck\Collection\Doctrine\ORM\ObjectRepository;
use Zenstruck\Collection\Doctrine\ORM\Repository\EntityRepositoryMixin;
use Zenstruck\Collection\Doctrine\ORM\Repository\Flushable;
use Zenstruck\Collection\Doctrine\ORM\Repository\Removable;
use Zenstruck\Collection\Doctrine\ORM\Repository\Writable;
use Zenstruck\Collection\Paginatable;
use Zenstruck\Collection\Tests\Doctrine\Fixture\Entity;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class KitchenSinkRepository extends ObjectRepository implements Collection
{
    use Flushable, Writable, Removable, EntityRepositoryMixin, Paginatable;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function take(int $limit, int $offset = 0): Collection
    {
        return static::createResult($this->qb())->take($limit, $offset);
    }

    protected static function className(): string
    {
        return Entity::class;
    }

    protected function em(): EntityManagerInterface
    {
        return $this->em;
    }
}
