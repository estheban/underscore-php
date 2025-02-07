<?php

/*
 * This file is part of Underscore.php
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Underscore\Types;

use Underscore\Dummies\DummyDefault;
use Underscore\UnderscoreTestCase;

class ObjectsTest extends UnderscoreTestCase
{
    public function testCanCreateObject()
    {
        $object = Objects::create();

        $this->assertInstanceOf('stdClass', $object->obtain());
    }

    public function testCanObjectifyAnArray()
    {
        $object = Objects::from(['foo' => 'bar']);
        $this->assertEquals('bar', $object->foo);

        $object->bis = 'ter';
        $this->assertEquals('ter', $object->bis);

        $this->assertEquals(['foo' => 'bar', 'bis' => 'ter'], (array) $object->obtain());
    }

    public function testCanGetKeys()
    {
        $object = Objects::keys($this->object);

        $this->assertEquals(['foo', 'bis'], $object);
    }

    public function testCanGetValues()
    {
        $object = Objects::Values($this->object);

        $this->assertEquals(['bar', 'ter'], $object);
    }

    public function testCanGetMethods()
    {
        $methods = [
            'getDefault',
            'toArray',
            '__construct',
            '__toString',
            'create',
            'from',
            '__get',
            '__set',
            'isEmpty',
            'setSubject',
            'obtain',
            'extend',
            '__callStatic',
            '__call',
        ];

        $this->assertEquals($methods, Objects::methods(new DummyDefault()));
    }

    public function testCanPluckColumns()
    {
        $object = Objects::pluck($this->objectMulti, 'foo');
        $matcher = (object) ['bar', 'bar', null];

        $this->assertEquals($matcher, $object);
    }

    public function testCanSetValues()
    {
        $object = (object) ['foo' => ['foo' => 'bar'], 'bar' => 'bis'];
        $object = Objects::set($object, 'foo.bar.bis', 'ter');

        $this->assertEquals('ter', $object->foo['bar']['bis']);
        $this->assertObjectHasAttribute('bar', $object);
    }

    public function testCanRemoveValues()
    {
        $array = Objects::remove($this->objectMulti, '0.foo');
        $matcher = (array) $this->objectMulti;
        unset($matcher[0]->foo);

        $this->assertEquals((object) $matcher, $array);
    }

    public function testCanConvertToJson()
    {
        $under = Objects::toJSON($this->object);

        $this->assertEquals('{"foo":"bar","bis":"ter"}', $under);
    }

    public function testCanSort()
    {
        $child = (object) ['sort' => 5];
        $child_alt = (object) ['sort' => 12];
        $object = (object) ['name' => 'foo', 'age' => 18, 'child' => $child];
        $object_alt = (object) ['name' => 'bar', 'age' => 21, 'child' => $child_alt];
        $collection = [$object, $object_alt];

        $under = Objects::sort($collection, 'name', 'asc');
        $this->assertEquals([$object_alt, $object], $under);

        $under = Objects::sort($collection, 'child.sort', 'desc');
        $this->assertEquals([$object_alt, $object], $under);

        $under = Objects::sort($collection, function ($value) {
            return $value->child->sort;
        }, 'desc');
        $this->assertEquals([$object_alt, $object], $under);
    }

    public function testCanConvertToArray()
    {
        $object = Objects::toArray($this->object);

        $this->assertEquals($this->array, $object);
    }

    public function testCanUnpackObjects()
    {
        $multi = (object) ['attributes' => ['name' => 'foo', 'age' => 18]];
        $objectAuto = Objects::unpack($multi);
        $objectManual = Objects::unpack($multi, 'attributes');

        $this->assertObjectHasAttribute('name', $objectAuto);
        $this->assertObjectHasAttribute('age', $objectAuto);
        $this->assertEquals('foo', $objectAuto->name);
        $this->assertEquals(18, $objectAuto->age);
        $this->assertEquals($objectManual, $objectAuto);
    }

    public function testCanReplaceValues()
    {
        $object = Objects::replace($this->object, 'foo', 'notfoo', 'notbar');
        $matcher = (object) ['notfoo' => 'notbar', 'bis' => 'ter'];

        $this->assertEquals($matcher, $object);
    }

    public function testCanSetAnGetValues()
    {
        $object = $this->object;
        $getset = Objects::setAndGet($object, 'set', 'get');
        $get = Objects::get($object, 'set');

        $this->assertEquals($getset, 'get');
        $this->assertEquals($get, $getset);
    }

    public function testFilterBy()
    {
        $a = [
            (object) ['id' => 123, 'name' => 'foo', 'group' => 'primary', 'value' => 123456],
            (object) ['id' => 456, 'name' => 'bar', 'group' => 'primary', 'value' => 1468],
            (object) ['id' => 499, 'name' => 'baz', 'group' => 'secondary', 'value' => 2365],
            (object) ['id' => 789, 'name' => 'ter', 'group' => 'primary', 'value' => 2468],
        ];

        $b = Objects::filterBy($a, 'name', 'baz');
        $this->assertCount(1, $b);
        $this->assertEquals(2365, $b[0]->value);

        $c = Objects::filterBy($a, 'value', 2468);
        $this->assertCount(1, $c);
        $this->assertEquals('primary', $c[0]->group);

        $d = Objects::filterBy($a, 'group', 'primary');
        $this->assertCount(3, $d);

        $e = Objects::filterBy($a, 'value', 2000, 'lt');
        $this->assertCount(1, $e);
        $this->assertEquals(1468, $e[0]->value);
    }

    public function testFindBy()
    {
        $a = [
            (object) ['id' => 123, 'name' => 'foo', 'group' => 'primary', 'value' => 123456],
            (object) ['id' => 456, 'name' => 'bar', 'group' => 'primary', 'value' => 1468],
            (object) ['id' => 499, 'name' => 'baz', 'group' => 'secondary', 'value' => 2365],
            (object) ['id' => 789, 'name' => 'ter', 'group' => 'primary', 'value' => 2468],
        ];

        $b = Objects::findBy($a, 'name', 'baz');
        $this->assertInstanceOf('\stdClass', $b);
        $this->assertEquals(2365, $b->value);
        $this->assertObjectHasAttribute('name', $b);
        $this->assertObjectHasAttribute('group', $b);
        $this->assertObjectHasAttribute('value', $b);

        $c = Objects::findBy($a, 'value', 2468);
        $this->assertInstanceOf('\stdClass', $c);
        $this->assertEquals('primary', $c->group);

        $d = Objects::findBy($a, 'group', 'primary');
        $this->assertInstanceOf('\stdClass', $d);
        $this->assertEquals('foo', $d->name);

        $e = Objects::findBy($a, 'value', 2000, 'lt');
        $this->assertInstanceOf('\stdClass', $e);
        $this->assertEquals(1468, $e->value);
    }
}
