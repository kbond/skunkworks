<?php

namespace Zenstruck\Collection\Tests\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Zenstruck\Collection\Doctrine\ORM\Batch\CountableBatchIterator;
use Zenstruck\Collection\Doctrine\ORM\Batch\CountableBatchProcessor;
use Zenstruck\Collection\Tests\Doctrine\Fixture\Entity;
use Zenstruck\Collection\Tests\Doctrine\HasDatabase;
use Zenstruck\Collection\Tests\Doctrine\MatchableRepositoryTests;
use Zenstruck\Collection\Tests\Doctrine\ORM\Fixture\KitchenSinkRepository;
use Zenstruck\Collection\Tests\PagintableCollectionTests;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RepositoryTest extends TestCase
{
    use HasDatabase, MatchableRepositoryTests, PagintableCollectionTests;

    /**
     * @test
     */
    public function can_find(): void
    {
        $repo = $this->createWithItems(3);

        $this->assertEquals($this->expectedValueAt(2), $repo->find(2));
        $this->assertNull($repo->find(99));
    }

    /**
     * @test
     */
    public function can_find_all(): void
    {
        $repo = $this->createWithItems(2);

        $this->assertEquals([$this->expectedValueAt(1), $this->expectedValueAt(2)], $repo->findAll());
    }

    /**
     * @test
     */
    public function find_all_is_empty_if_repository_is_empty(): void
    {
        $this->assertSame([], $this->createWithItems(0)->findAll());
    }

    /**
     * @test
     */
    public function can_find_by(): void
    {
        $repo = $this->createWithItems(2);

        $this->assertEquals([$this->expectedValueAt(1), $this->expectedValueAt(2)], $repo->findBy([]));
        $this->assertEquals([$this->expectedValueAt(2), $this->expectedValueAt(1)], $repo->findBy([], ['id' => 'DESC']));
        $this->assertEquals([$this->expectedValueAt(2)], $repo->findBy([], ['id' => 'DESC'], 1));
        $this->assertEquals([$this->expectedValueAt(1)], $repo->findBy([], ['id' => 'DESC'], 1, 1));
        $this->assertEquals([$this->expectedValueAt(2)], $repo->findBy(['id' => 2]));
        $this->assertSame([], $repo->findBy(['id' => 99]));
    }

    /**
     * @test
     */
    public function can_find_one_by(): void
    {
        $repo = $this->createWithItems(2);

        $this->assertEquals($this->expectedValueAt(2), $repo->findOneBy(['id' => 2]));
        $this->assertNull($repo->findOneBy(['id' => 99]));
    }

    /**
     * @test
     */
    public function can_call_method_on_inner_entity_repository(): void
    {
        $this->assertInstanceOf(QueryBuilder::class, $this->createWithItems(0)->createQueryBuilder('e'));
    }

    /**
     * @test
     */
    public function can_add_and_flush(): void
    {
        $repo = $this->createWithItems(0);

        $this->assertCount(0, $repo);

        $repo->add(new Entity('foo'), false);
        $repo->add(new Entity('bar'), false);
        $repo->flush();
        $repo->add(new Entity('baz'));

        $this->assertCount(3, $repo);
    }

    /**
     * @test
     */
    public function can_remove_and_flush(): void
    {
        $repo = $this->createWithItems(3);
        $items = $repo->findAll();

        $this->assertCount(3, $repo);

        $repo->remove($items[0], false);
        $repo->remove($items[1], false);
        $repo->flush();
        $repo->remove($items[2]);

        $this->assertEmpty($repo);
    }

    /**
     * @test
     */
    public function can_get_batch_iterator(): void
    {
        $iterator = $this->createWithItems(3)->batch();

        $this->assertInstanceOf(CountableBatchIterator::class, $iterator);
        $this->assertCount(3, $iterator);
    }

    /**
     * @test
     */
    public function can_get_batch_processor(): void
    {
        $iterator = $this->createWithItems(3)->batchProcess();

        $this->assertInstanceOf(CountableBatchProcessor::class, $iterator);
        $this->assertCount(3, $iterator);
    }

    /**
     * @test
     */
    public function detaches_entities_from_em_on_iterate(): void
    {
        $iterator = $this->createWithItems(3);

        $result = \iterator_to_array($iterator)[0];

        $this->assertInstanceOf(Entity::class, $result);
        $this->assertFalse($this->em->contains($result));
    }

    /**
     * @test
     */
    public function can_match_for_callback(): void
    {
        $object = $this->createWithItems(3)->matchOne(function(QueryBuilder $qb, $alias) {
            $qb->where("{$alias}.value = 'value 2'");
        });

        $this->assertSame('value 2', $object->value);
    }

    protected function createWithItems(int $count): KitchenSinkRepository
    {
        $this->persistEntities($count);

        return new KitchenSinkRepository($this->em);
    }

    protected function expectedValueAt(int $position): Entity
    {
        return new Entity("value {$position}", $position);
    }
}
