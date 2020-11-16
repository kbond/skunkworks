<?php

namespace Zenstruck\Collection\Tests\Bridge\Doctrine\Collection;

use Zenstruck\Collection\Bridge\Doctrine\CollectionDecorator;
use Zenstruck\Collection\Tests\Bridge\Doctrine\CollectionDecoratorTest;
use Zenstruck\Collection\Tests\Fixture\Iterator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class IteratorCollectionDecoratorTest extends CollectionDecoratorTest
{
    protected function createWithItems(int $count): CollectionDecorator
    {
        return new CollectionDecorator(new Iterator($count));
    }
}
