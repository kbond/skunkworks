<?php

namespace Zenstruck\Collection\Tests\Doctrine;

use Zenstruck\Collection\Exception\NotFound;
use Zenstruck\Collection\Spec;
use Zenstruck\Collection\Specification\Nested;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait MatchableRepositoryTests
{
    /**
     * @test
     */
    public function match_and_x_composite(): void
    {
        $repo = $this->createWithItems(3);

        $objects = $repo->match(
            Spec::andX(
                Spec::gt('id', 1),
                Spec::lt('id', 3)
            )
        );

        $this->assertCount(1, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
    }

    /**
     * @test
     */
    public function match_or_x_composite(): void
    {
        $objects = $this->createWithItems(3)->match(
            Spec::orX(
                Spec::lt('id', 2),
                Spec::gt('id', 2)
            )
        );

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_like(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::like('value', 'value 2'));

        $this->assertCount(1, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
    }

    /**
     * @test
     */
    public function match_like_wildcard(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::like('value', 'value *')->allowWildcard());

        $this->assertCount(3, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 2', \iterator_to_array($objects)[1]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[2]->value);
    }

    /**
     * @test
     */
    public function match_contains(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::contains('value', 'value'));

        $this->assertCount(3, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 2', \iterator_to_array($objects)[1]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[2]->value);
    }

    /**
     * @test
     */
    public function match_begins_with(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::beginsWith('value', 'v'));

        $this->assertCount(3, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 2', \iterator_to_array($objects)[1]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[2]->value);
    }

    /**
     * @test
     */
    public function match_ends_with(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::endsWith('value', '2'));

        $this->assertCount(1, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
    }

    /**
     * @test
     */
    public function match_not_like(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::notLike('value', 'value 2'));

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_not_like_wildcard(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::notLike('value', 'value *')->allowWildcard());

        $this->assertEmpty($objects);
    }

    /**
     * @test
     */
    public function match_not_contains(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::notContains('value', 'value'));

        $this->assertEmpty($objects);
    }

    /**
     * @test
     */
    public function match_not_beginning_with(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::notBeginningWith('value', 'value'));

        $this->assertEmpty($objects);
    }

    /**
     * @test
     */
    public function match_not_ends_with(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::notEndingWith('value', '2'));

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_equal(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::eq('value', 'value 2'));

        $this->assertCount(1, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
    }

    /**
     * @test
     */
    public function match_not_equal(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::neq('value', 'value 2'));

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_is_null(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::isNull('value'));

        $this->assertEmpty($objects);
    }

    /**
     * @test
     */
    public function match_is_not_null(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::isNotNull('value'));

        $this->assertCount(3, $objects);
    }

    /**
     * @test
     */
    public function match_in_string(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::in('value', ['value 1', 'value 3']));

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_in_int(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::in('id', [1, 3]));

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_in_numeric_string(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::in('id', ['1', '3']));

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_in_mixed_str_field(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::in('value', ['1', 'value 2', 3]));

        $this->assertCount(1, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
    }

    /**
     * @test
     */
    public function match_in_mixed_int_field(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::in('id', ['1', 'value 2', 3]));

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_not_in_string(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::notIn('value', ['value 1', 'value 3']));

        $this->assertCount(1, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
    }

    /**
     * @test
     */
    public function match_not_in_int(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::notIn('id', [1, 3]));

        $this->assertCount(1, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
    }

    /**
     * @test
     */
    public function match_not_in_numeric_string(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::notIn('id', ['1', '3']));

        $this->assertCount(1, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
    }

    /**
     * @test
     */
    public function match_not_in_mixed_str_field(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::notIn('value', ['1', 'value 2', 3]));

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_not_in_mixed_int_field(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::notIn('id', ['1', 'value 2', 3]));

        $this->assertCount(1, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
    }

    /**
     * @test
     */
    public function match_less_than(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::lt('id', 3));

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 2', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_less_than_equal(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::lte('id', 2));

        $this->assertCount(2, $objects);
        $this->assertSame('value 1', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 2', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_greater_than(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::gt('id', 1));

        $this->assertCount(2, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_greater_than_equal(): void
    {
        $objects = $this->createWithItems(3)->match(Spec::gte('id', 2));

        $this->assertCount(2, $objects);
        $this->assertSame('value 2', \iterator_to_array($objects)[0]->value);
        $this->assertSame('value 3', \iterator_to_array($objects)[1]->value);
    }

    /**
     * @test
     */
    public function match_sort_desc(): void
    {
        $objects = \iterator_to_array($this->createWithItems(3)->match(Spec::sortDesc('value')));

        $this->assertSame('value 3', $objects[0]->value);
        $this->assertSame('value 2', $objects[1]->value);
        $this->assertSame('value 1', $objects[2]->value);
    }

    /**
     * @test
     */
    public function match_sort_asc(): void
    {
        $objects = \iterator_to_array($this->createWithItems(3)->match(Spec::sortAsc('value')));

        $this->assertSame('value 1', $objects[0]->value);
        $this->assertSame('value 2', $objects[1]->value);
        $this->assertSame('value 3', $objects[2]->value);
    }

    /**
     * @test
     */
    public function match_composite_order_by(): void
    {
        $objects = \iterator_to_array($this->createWithItems(3)->match(
            Spec::andX(
                Spec::gt('id', 1),
                Spec::sortDesc('id')
            )
        ));

        $this->assertCount(2, $objects);
        $this->assertSame('value 3', $objects[0]->value);
        $this->assertSame('value 2', $objects[1]->value);
    }

    /**
     * @test
     */
    public function match_one_for_single_comparison(): void
    {
        $object = $this->createWithItems(3)->matchOne(Spec::eq('value', 'value 2'));

        $this->assertSame('value 2', $object->value);
    }

    /**
     * @test
     */
    public function not_found_exception_found_for_match_one_if_no_result(): void
    {
        $this->expectException(NotFound::class);

        $this->createWithItems(3)->matchOne(Spec::eq('value', 'value 6'));
    }

    /**
     * @test
     */
    public function can_use_nested_specification(): void
    {
        $object = $this->createWithItems(3)->matchOne(new class() implements Nested {
            public function child()
            {
                return Spec::eq('value', 'value 2');
            }
        });

        $this->assertSame('value 2', $object->value);
    }
}
