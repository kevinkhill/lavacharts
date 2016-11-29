<?php

namespace Khill\Lavacharts\Tests\Support;

class Foo {
    public $fooVar = 1;
}

class Bar {
    public $barVar = 2;
}

class ArrayReduceTest extends \PHPUnit_Framework_TestCase
{
    public $mixedTypes;
    public $randomTypes;
    public $sameTypes;

    public function setUp()
    {
        parent::setUp();

        $this->sameTypes = [
            new Foo, new Foo, new Foo
        ];

        $this->mixedTypes = [
            new Foo, new Bar, new Foo
        ];

        $this->randomTypes = [
            new Foo, [], new Foo, 5
        ];
    }

    public function testArrayReduceWithSameType()
    {
        $check = array_reduce($this->sameTypes, function ($prev, $curr) {
            return $prev && $curr instanceof Foo;
        }, true);

        $this->assertTrue($check);
    }

    public function testArrayReduceWithMixedTypes()
    {
        $check = array_reduce($this->mixedTypes, function ($prev, $curr) {
            return $prev && $curr instanceof Foo;
        }, true);

        $this->assertFalse($check);
    }

    public function testArrayReduceWithRandomTypes()
    {
        $check = array_reduce($this->randomTypes, function ($prev, $curr) {
            return $prev && $curr instanceof Foo;
        }, true);

        $this->assertFalse($check);
    }
}
