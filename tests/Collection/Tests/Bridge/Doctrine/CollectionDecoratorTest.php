<?php

namespace Zenstruck\Collection\Tests\Bridge\Doctrine;

use PHPUnit\Framework\TestCase;
use Zenstruck\Collection\Bridge\Doctrine\CollectionDecorator;
use Zenstruck\Collection\Tests\PagintableCollectionTests;

/**
 * @source https://github.com/doctrine/collections/blob/fb7a59b4d2c57f7c992428cfd70a0cc501d6f220/tests/Doctrine/Tests/Common/Collections/BaseCollectionTest.php
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class CollectionDecoratorTest extends TestCase
{
    use PagintableCollectionTests;

    private ?CollectionDecorator $collection = null;

    protected function setUp(): void
    {
        $this->collection = new CollectionDecorator();
    }

    /**
     * @test
     */
    public function can_construct_with_null(): void
    {
        $this->assertEmpty(new CollectionDecorator(null));
    }

    /**
     * @test
     */
    public function must_be_constructed_with_iterator(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CollectionDecorator('not iterable');
    }

    /**
     * @test
     */
    public function key_current_next(): void
    {
        $collection = new CollectionDecorator(['a', 'b']);

        $this->assertSame(0, $collection->key());
        $this->assertSame('a', $collection->current());
        $this->assertSame('b', $collection->next());
    }

    /**
     * @test
     */
    public function isset_and_unset(): void
    {
        $this->assertFalse(isset($this->collection[0]));
        $this->collection->add('testing');
        $this->assertTrue(isset($this->collection[0]));
        unset($this->collection[0]);
        $this->assertFalse(isset($this->collection[0]));
    }

    /**
     * @test
     */
    public function removing_non_existent_entry_returns_null(): void
    {
        $this->assertNull($this->collection->remove('testing_does_not_exist'));
    }

    /**
     * @test
     */
    public function exists(): void
    {
        $this->collection->add('one');
        $this->collection->add('two');
        $exists = $this->collection->exists(static function($k, $e) {
            return 'one' === $e;
        });
        $this->assertTrue($exists);
        $exists = $this->collection->exists(static function($k, $e) {
            return 'other' === $e;
        });
        $this->assertFalse($exists);
    }

    /**
     * @test
     */
    public function map(): void
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $res = $this->collection->map(static function($e) {
            return $e * 2;
        });
        $this->assertEquals([2, 4], $res->toArray());
    }

    /**
     * @test
     */
    public function filter(): void
    {
        $this->collection->add(1);
        $this->collection->add('foo');
        $this->collection->add(3);
        $res = $this->collection->filter(static function($e) {
            return \is_numeric($e);
        });
        $this->assertEquals([0 => 1, 2 => 3], $res->toArray());
    }

    /**
     * @test
     */
    public function filter_by_value_and_key(): void
    {
        $this->collection->add(1);
        $this->collection->add('foo');
        $this->collection->add(3);
        $this->collection->add(4);
        $this->collection->add(5);
        $res = $this->collection->filter(static function($v, $k) {
            return \is_numeric($v) && 0 === $k % 2;
        });
        $this->assertSame([0 => 1, 2 => 3, 4 => 5], $res->toArray());
    }

    /**
     * @test
     */
    public function first_and_last(): void
    {
        $this->collection->add('one');
        $this->collection->add('two');

        $this->assertEquals($this->collection->first(), 'one');
        $this->assertEquals($this->collection->last(), 'two');
    }

    /**
     * @test
     */
    public function array_access(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        $this->assertEquals($this->collection[0], 'one');
        $this->assertEquals($this->collection[1], 'two');

        unset($this->collection[0]);
        $this->assertEquals($this->collection->count(), 1);
    }

    /**
     * @test
     */
    public function contains_key(): void
    {
        $this->collection[5] = 'five';
        $this->assertTrue($this->collection->containsKey(5));
    }

    /**
     * @test
     */
    public function call_contains(): void
    {
        $this->collection[0] = 'test';
        $this->assertTrue($this->collection->contains('test'));
    }

    /**
     * @test
     */
    public function search(): void
    {
        $this->collection[0] = 'test';
        $this->assertEquals(0, $this->collection->indexOf('test'));
    }

    /**
     * @test
     */
    public function get(): void
    {
        $this->collection[0] = 'test';
        $this->assertEquals('test', $this->collection->get(0));
    }

    /**
     * @test
     */
    public function get_keys(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals([0, 1], $this->collection->getKeys());
    }

    /**
     * @test
     */
    public function get_values(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals(['one', 'two'], $this->collection->getValues());
    }

    /**
     * @test
     */
    public function can_count(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals($this->collection->count(), 2);
        $this->assertEquals(\count($this->collection), 2);
    }

    /**
     * @test
     */
    public function for_all(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals($this->collection->forAll(static function($k, $e) {
            return \is_string($e);
        }), true);
        $this->assertEquals($this->collection->forAll(static function($k, $e) {
            return \is_array($e);
        }), false);
    }

    /**
     * @test
     */
    public function partition(): void
    {
        $this->collection[] = true;
        $this->collection[] = false;
        $partition = $this->collection->partition(static function($k, $e) {
            return true === $e;
        });
        $this->assertEquals($partition[0][0], true);
        $this->assertEquals($partition[1][0], false);
    }

    /**
     * @test
     */
    public function clear(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection->clear();
        $this->assertEquals($this->collection->isEmpty(), true);
    }

    /**
     * @test
     */
    public function remove(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $el = $this->collection->remove(0);

        $this->assertEquals('one', $el);
        $this->assertEquals($this->collection->contains('one'), false);
        $this->assertNull($this->collection->remove(0));
    }

    /**
     * @test
     */
    public function remove_element(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        $this->assertTrue($this->collection->removeElement('two'));
        $this->assertFalse($this->collection->contains('two'));
        $this->assertFalse($this->collection->removeElement('two'));
    }

    /**
     * @test
     */
    public function slice(): void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection[] = 'three';

        $slice = $this->collection->slice(0, 1);
        $this->assertIsArray($slice);
        $this->assertEquals(['one'], $slice);

        $slice = $this->collection->slice(1);
        $this->assertEquals([1 => 'two', 2 => 'three'], $slice);

        $slice = $this->collection->slice(1, 1);
        $this->assertEquals([1 => 'two'], $slice);
    }

    /**
     * @test
     */
    public function can_remove_null_values_by_key(): void
    {
        $this->collection->add(null);
        $this->collection->remove(0);
        $this->assertTrue($this->collection->isEmpty());
    }

    /**
     * @test
     */
    public function can_verify_existing_keys_with_null_values(): void
    {
        $this->collection->set('key', null);
        $this->assertTrue($this->collection->containsKey('key'));
    }

    protected function fillMatchingFixture(): void
    {
        $std1 = new \stdClass();
        $std1->foo = 'bar';
        $this->collection[] = $std1;

        $std2 = new \stdClass();
        $std2->foo = 'baz';
        $this->collection[] = $std2;
    }

    abstract protected function createWithItems(int $count): CollectionDecorator;
}
