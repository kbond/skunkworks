<?php

namespace Zenstruck\Utilities\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Utilities\ArrayAccessor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ArrayAccessorTest extends TestCase
{
    /**
     * @author Taylor Otwell <taylor@laravel.com>
     * @source https://github.com/laravel/framework/blob/56f79e2b6e4ee99b26be0c5c73d57cd3a3974fd1/tests/Support/SupportArrTest.php#L249
     *
     * @test
     */
    public function can_get(): void
    {
        $array = new ArrayAccessor(['products.desk' => ['price' => 100]]);
        $this->assertSame(['price' => 100], $array->get('products.desk'));

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertSame(['price' => 100], $array->get('products.desk'));

        // Test null array values
        $array = new ArrayAccessor(['foo' => null, 'bar' => ['baz' => null]]);
        $this->assertNull($array->get('foo', 'default'));
        $this->assertNull($array->get('bar.baz', 'default'));

        // Test numeric keys
        $array = new ArrayAccessor([
            'products' => [
                ['name' => 'desk'],
                ['name' => 'chair'],
            ],
        ]);
        $this->assertSame('desk', $array->get('products.0.name'));
        $this->assertSame('chair', $array->get('products.1.name'));

        // Test return default value for non-existing key.
        $array = new ArrayAccessor(['names' => ['developer' => 'taylor']]);
        $this->assertSame('dayle', $array->get('names.otherDeveloper', 'dayle'));
        $this->assertSame('dayle', $array->get('names.otherDeveloper', function() { return 'dayle'; }));
    }

    /**
     * @test
     */
    public function if_default_is_throwable_it_is_thrown_when_not_found(): void
    {
        $throwable = new \RuntimeException();
        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);

        $this->assertSame(100, $array->get('products.desk.price', $throwable));

        try {
            $array->get('does.not.exist', $throwable);
        } catch (\RuntimeException $e) {
            $this->assertSame($throwable, $e);

            return;
        }

        $this->fail('Exception not thrown.');
    }

    /**
     * @author Taylor Otwell <taylor@laravel.com>
     * @source https://github.com/laravel/framework/blob/56f79e2b6e4ee99b26be0c5c73d57cd3a3974fd1/tests/Support/SupportArrTest.php#L333
     *
     * @test
     */
    public function can_has(): void
    {
        $array = new ArrayAccessor(['products.desk' => ['price' => 100]]);
        $this->assertTrue($array->has('products.desk'));

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertTrue($array->has('products.desk'));
        $this->assertTrue($array->has('products.desk.price'));
        $this->assertFalse($array->has('products.foo'));
        $this->assertFalse($array->has('products.desk.foo'));

        $array = new ArrayAccessor(['foo' => null, 'bar' => ['baz' => null]]);
        $this->assertTrue($array->has('foo'));
        $this->assertTrue($array->has('bar.baz'));

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertTrue($array->has('products.desk'));
        $this->assertTrue($array->has('products.desk', 'products.desk.price'));
        $this->assertTrue($array->has('products', 'products'));
        $this->assertFalse($array->has('foo'));
        $this->assertFalse($array->has());
        $this->assertFalse($array->has('products.desk', 'products.price'));

        $array = new ArrayAccessor([
            'products' => [
                ['name' => 'desk'],
            ],
        ]);
        $this->assertTrue($array->has('products.0.name'));
        $this->assertFalse($array->has('products.0.price'));

        $this->assertTrue((new ArrayAccessor(['' => 'some']))->has(''));
        $this->assertFalse((new ArrayAccessor(['']))->has(''));
        $this->assertFalse((new ArrayAccessor([]))->has(''));
        $this->assertFalse((new ArrayAccessor([]))->has('', ''));
    }

    /**
     * @author Taylor Otwell <taylor@laravel.com>
     * @source https://github.com/laravel/framework/blob/56f79e2b6e4ee99b26be0c5c73d57cd3a3974fd1/tests/Support/SupportArrTest.php#L680
     *
     * @test
     */
    public function can_set(): void
    {
        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertSame(['products' => ['desk' => ['price' => 200]]], $array->set('products.desk.price', 200)->all());

        // The key doesn't exist at the depth
        $array = new ArrayAccessor(['products' => 'desk']);
        $this->assertSame(['products' => ['desk' => ['price' => 200]]], $array->set('products.desk.price', 200)->all());

        // No corresponding key exists
        $array = new ArrayAccessor(['products']);
        $this->assertSame(['products', 'products' => ['desk' => ['price' => 200]]], $array->set('products.desk.price', 200)->all());

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertSame(['products' => ['desk' => ['price' => 100]], 'table' => 500], $array->set('table', 500)->all());

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertSame(['products' => ['desk' => ['price' => 100]], 'table' => ['price' => 350]], $array->set('table.price', 350)->all());

        $array = new ArrayAccessor();
        $this->assertSame(['products' => ['desk' => ['price' => 200]]], $array->set('products.desk.price', 200)->all());

        // Override
        $array = new ArrayAccessor(['products' => 'table']);
        $this->assertSame(['products' => ['desk' => ['price' => 300]]], $array->set('products.desk.price', 300)->all());
    }

    /**
     * @author Taylor Otwell <taylor@laravel.com>
     * @source https://github.com/laravel/framework/blob/56f79e2b6e4ee99b26be0c5c73d57cd3a3974fd1/tests/Support/SupportArrTest.php#L833
     *
     * @test
     */
    public function can_unset(): void
    {
        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertSame(['products' => ['desk' => ['price' => 100]]], $array->unset()->all());

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertSame(['products' => ['desk' => ['price' => 100]]], $array->unset('invalid', 'inva.lid')->all());

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertSame(['products' => []], $array->unset('products.desk')->all());

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertSame(['products' => ['desk' => []]], $array->unset('products.desk.price')->all());

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]]);
        $this->assertSame(['products' => ['desk' => ['price' => 100]]], $array->unset('products.final.price')->all());

        $array = new ArrayAccessor(['shop' => ['cart' => [150 => 0]]]);
        $this->assertSame(['shop' => ['cart' => [150 => 0]]], $array->unset('shop.final.cart')->all());

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]]);
        $this->assertSame(['products' => ['desk' => ['price' => ['original' => 50]]]], $array->unset('products.desk.price.taxes')->all());

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]]);
        $this->assertSame(['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]], $array->unset('products.desk.final.taxes')->all());

        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 50], null => 'something']]);
        $this->assertSame(['products' => ['desk' => [], null => 'something']], $array->unset('products.amount.all', 'products.desk.price')->all());

        // Only works on first level keys
        $array = new ArrayAccessor(['joe@example.com' => 'Joe', 'jane@example.com' => 'Jane']);
        $this->assertSame(['jane@example.com' => 'Jane'], $array->unset('joe@example.com')->all());

        // Does not work for nested keys
        $array = new ArrayAccessor(['emails' => ['joe@example.com' => ['name' => 'Joe'], 'jane@localhost' => ['name' => 'Jane']]]);
        $this->assertSame(['emails' => ['joe@example.com' => ['name' => 'Joe']]], $array->unset('emails.joe@example.com', 'emails.jane@localhost')->all());

        // alternate delimiter
        $array = new ArrayAccessor(['products' => ['desk' => ['price' => 100]]], '/');
        $this->assertSame(['products' => []], $array->unset('products/desk')->all());
    }

    /**
     * @test
     */
    public function can_use_numerical_keys(): void
    {
        $array = new ArrayAccessor(['a', ['b', 'c']]);

        $this->assertSame('a', $array->get(0));
        $this->assertSame('a', $array->get('0'));
        $this->assertSame(['b', 'c'], $array->get(1));
        $this->assertSame(['b', 'c'], $array->get('1'));
        $this->assertSame('c', $array->get(1.1));
        $this->assertSame('c', $array->get('1.1'));

        $array->set(1.1, 'd');

        $this->assertSame('d', $array->get(1.1));
    }

    /**
     * @test
     */
    public function can_use_array_access(): void
    {
        $array = new ArrayAccessor();

        $array['a.b'] = 'c';

        $this->assertSame(['a' => ['b' => 'c']], $array->all());

        $this->assertTrue(isset($array['a.b']));
        $this->assertFalse(isset($array['a.c']));

        $this->assertSame('c', $array['a.b']);

        unset($array['a.b'], $array['invalid.path']);

        $this->assertSame([], $array['a']);
    }
}
