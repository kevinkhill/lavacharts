<?php

namespace Khill\Lavacharts\Tests\Support;

use ArrayIterator;
use Khill\Lavacharts\Support\Contracts\IterableArray;
use Khill\Lavacharts\Support\Traits\IterableArrayAccessTrait;

class ArrayAccessPizzaTestClass implements IterableArray
{
    use IterableArrayAccessTrait;

    public $options = [
        'crust' => 'deep dish',
        'sauce' => 'red',
        'toppings' => [
            'cheese',
            'pepperoni'
        ]
    ];

    public function getArrayAccessProperty()
    {
        return 'options';
    }
}

class ArrayAccessTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayAccessPizzaTestClass
     */
    public $pizza;

    protected function setUp()
    {
        $this->pizza = new ArrayAccessPizzaTestClass;
    }

    public function testGetArrayAccessProperty()
    {
        $this->assertEquals('options', $this->pizza->getArrayAccessProperty());
    }

    public function testGetIterator()
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->pizza->getIterator());
    }

    /**
     * @depends testGetArrayAccessProperty
     */
    public function testArrayAccessOffsetExists()
    {
        $this->assertTrue(isset($this->pizza['sauce']));
    }

    /**
     * @depends testGetArrayAccessProperty
     * @depends testArrayAccessOffsetExists
     */
    public function testArrayAccessOffsetGet()
    {
        $this->assertEquals('red', $this->pizza['sauce']);
    }

    /**
     * @depends testGetArrayAccessProperty
     * @depends testArrayAccessOffsetGet
     */
    public function testArrayAccessNullOffsetSet()
    {
        $this->pizza[] = 'well-done';

        $this->assertEquals('well-done', array_pop($this->pizza->options));
    }

    /**
     * @depends testGetArrayAccessProperty
     * @depends testArrayAccessOffsetGet
     */
    public function testArrayAccessNamedOffsetSet()
    {
        $this->pizza['method'] = 'delivery';

        $this->assertArrayHasKey('method', $this->pizza);
        $this->assertEquals('delivery', $this->pizza['method']);
    }

    /**
     * @depends testGetArrayAccessProperty
     * @depends testArrayAccessOffsetGet
     * @depends testArrayAccessOffsetExists
     */
    public function testArrayAccessOffsetUnset()
    {
        unset($this->pizza['toppings']);

        $this->assertFalse(isset($this->pizza['toppings']));
    }

    /**
     * @depends testGetIterator
     */
    public function testUsingArrayIteratorWithForEach()
    {
        foreach ($this->pizza as $key => $option) {
            $this->assertTrue(is_string($key));
        }
    }
}
