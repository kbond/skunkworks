<?php

namespace Zenstruck\Utilities\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Utilities\DotAccessor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class DotAccessorTest extends TestCase
{
    /**
     * @author Taylor Otwell <taylor@laravel.com>
     * @source https://github.com/laravel/framework/blob/56f79e2b6e4ee99b26be0c5c73d57cd3a3974fd1/tests/Support/SupportArrTest.php#L249
     *
     * @test
     * @dataProvider getProvider
     */
    public function can_get($what, $path, $expected, $default = null): void
    {
        $this->assertSame($expected, (new DotAccessor($what))->get($path, $default));
    }

    public static function getProvider(): iterable
    {
        yield [['products.desk' => ['price' => 100]], 'products.desk', ['price' => 100]];
        yield [['products' => ['desk' => ['price' => 100]]], 'products.desk', ['price' => 100]];
        yield [['products' => ['desk' => ['price' => 100]]], 'products.desk.price', 100];
        yield [['foo' => null, 'bar' => ['baz' => null]], 'foo', null, 'default'];
        yield [['foo' => null, 'bar' => ['baz' => null]], 'bar.baz', null, 'default'];
        yield [['products' => [['name' => 'desk'], ['name' => 'chair']]], 'products.0.name', 'desk'];
        yield [['products' => [['name' => 'desk'], ['name' => 'chair']]], 'products.1.name', 'chair'];
        yield [['names' => ['developer' => 'taylor']], 'names.otherDeveloper', 'dayle', 'dayle'];
        yield [['obj' => SomeObject::create()], 'obj.prop1', 'a'];
        yield [['obj' => SomeObject::create()], 'obj.method1', 'c'];
        yield [['obj' => SomeObject::create()], 'obj.method2', 'd'];
        yield [['obj' => SomeObject::create()], 'obj.method3', 'e'];
        yield [['obj' => SomeObject::create()], 'obj.method4', 'f'];
        yield [SomeObject::create(), 'prop1', 'a'];
        yield [SomeObject::create(), 'method1', 'c'];
        yield [SomeObject::create(), 'method2', 'd'];
        yield [SomeObject::create(), 'method3', 'e'];
        yield [SomeObject::create(), 'method4', 'f'];
        yield [SomeObject::create(), 'prop2.v1', 'b'];
        yield [SomeObject::create(), 'prop2.self.prop1', 'a'];
        yield [SomeObject::create(), 'prop2.self.method5.prop1', 'a'];
        yield [['obj' => SomeObject::create()], 'obj.prop2.self.method5.prop1', 'a'];
        yield [['obj' => SomeObject::create()], 'obj.prop2.self.method5.invalid', null];
        yield [['obj' => SomeObject::create()], 'obj.prop2.self.invalid.prop1', null];
        yield [['obj' => SomeObject::create()], 'obj.prop2.self.method5.invalid', 'default', 'default'];
        yield [['obj' => SomeObject::create()], 'obj.prop2.self.invalid.prop1', 'default', 'default'];
    }

    /**
     * @test
     * @dataProvider hasProvider
     */
    public function can_has($what, $path, $expected): void
    {
        $this->assertSame($expected, (new DotAccessor($what))->has(...(array) $path));
    }

    public static function hasProvider(): iterable
    {
        yield [['products.desk' => ['price' => 100]], 'products.desk', true];
        yield [['products' => ['desk' => ['price' => 100]]], 'products.desk', true];
        yield [['products' => ['desk' => ['price' => 100]]], 'products.desk.price', true];
        yield [['foo' => null, 'bar' => ['baz' => null]], 'foo', true];
        yield [['foo' => null, 'bar' => ['baz' => null]], 'bar.baz', true];
        yield [['products' => [['name' => 'desk'], ['name' => 'chair']]], 'products.0.name', true];
        yield [['products' => [['name' => 'desk'], ['name' => 'chair']]], 'products.1.name', true];
        yield [['names' => ['developer' => 'taylor']], 'names.otherDeveloper', false];
        yield [['obj' => SomeObject::create()], 'obj.prop1', true];
        yield [['obj' => SomeObject::create()], 'obj.method1', true];
        yield [['obj' => SomeObject::create()], 'obj.method2', true];
        yield [['obj' => SomeObject::create()], 'obj.method3', true];
        yield [['obj' => SomeObject::create()], 'obj.method4', true];
        yield [SomeObject::create(), ['prop1', 'method1'], true];
        yield [SomeObject::create(), ['prop1', 'invalid'], false];
        yield [SomeObject::create(), 'prop1', true];
        yield [SomeObject::create(), 'method1', true];
        yield [SomeObject::create(), 'method2', true];
        yield [SomeObject::create(), 'method3', true];
        yield [SomeObject::create(), 'method4', true];
        yield [SomeObject::create(), 'prop2.v1', true];
        yield [SomeObject::create(), 'prop2.self.prop1', true];
        yield [SomeObject::create(), 'prop2.self.method5.prop1', true];
        yield [['obj' => SomeObject::create()], 'obj.prop2.self.method5.prop1', true];
        yield [['obj' => SomeObject::create()], 'obj.prop2.self.method5.invalid', false];
        yield [['obj' => SomeObject::create()], 'obj.prop2.self.invalid.prop1', false];
        yield [['obj' => SomeObject::create()], 'obj.prop2.self.method5.invalid', false];
        yield [['obj' => SomeObject::create()], 'obj.prop2.self.invalid.prop1', false];
    }

    /**
     * @test
     */
    public function array_passed_by_reference(): void
    {
        $array = ['foo' => ['bar' => 'baz']];
        $accessor = new DotAccessor($array);

        $this->assertSame('baz', $accessor->get('foo.bar'));

        $array['foo']['bar'] = 'new';

        $this->assertSame('new', $accessor->get('foo.bar'));
    }
}

class SomeObject
{
    public $prop1 = 'a';
    public $prop2 = ['v1' => 'b'];

    public static function create(): self
    {
        $object = new self();
        $object->prop2['self'] = new self();

        return $object;
    }

    public function method1(): string
    {
        return 'c';
    }

    public function getMethod2(): string
    {
        return 'd';
    }

    public function hasMethod3(): string
    {
        return 'e';
    }

    public function isMethod4(): string
    {
        return 'f';
    }

    public function method5(): self
    {
        return new self();
    }
}
