<?php

namespace Zenstruck\Collection\Tests\Bridge\Doctrine\Collection;

use Zenstruck\Collection\Bridge\Doctrine\CollectionDecorator;
use Zenstruck\Collection\Tests\Bridge\Doctrine\CollectionDecoratorTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ArrayCollectionDecoratorTest extends CollectionDecoratorTest
{
    protected function createWithItems(int $count): CollectionDecorator
    {
        return new CollectionDecorator($count ? \range(1, $count) : []);
    }
}
