<?php

namespace Zenstruck\Collection\Tests\Doctrine\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Zenstruck\Collection\Tests\Doctrine\DBAL\Fixture\ObjectRepository;
use Zenstruck\Collection\Tests\Doctrine\Fixture\Entity;
use Zenstruck\Collection\Tests\Doctrine\HasDatabase;
use Zenstruck\Collection\Tests\Doctrine\MatchableRepositoryTests;
use Zenstruck\Collection\Tests\PagintableCollectionTests;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ObjectRepositoryTest extends TestCase
{
    use HasDatabase, MatchableRepositoryTests, PagintableCollectionTests;

    /**
     * @test
     */
    public function can_match_for_callback(): void
    {
        $object = $this->createWithItems(3)->matchOne(function(QueryBuilder $qb) {
            $qb->where("value = 'value 2'");
        });

        $this->assertSame('value 2', $object->value);
    }

    protected function createWithItems(int $count): ObjectRepository
    {
        $this->persistEntities($count);

        return new ObjectRepository($this->em->getConnection());
    }

    protected function expectedValueAt(int $position): Entity
    {
        return new Entity("value {$position}", $position);
    }
}
