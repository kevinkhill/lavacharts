<?php

namespace Khill\Lavacharts\Tests\Support;

use Khill\Lavacharts\Support\Contracts\ArrayAccess;
use Khill\Lavacharts\Support\Traits\ArrayAccessTrait;

class ArrayAccessTestClass implements ArrayAccess
{
    use ArrayAccessTrait;

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
     * @var ArrayAccessTestClass
     */
    public $testClass;

    protected function setUp()
    {
        $this->testClass = new ArrayAccessTestClass;
    }

    public function testGetArrayAccessProperty()
    {
        $this->assertEquals('options', $this->testClass->getArrayAccessProperty());
    }

    /**
     * @depends testGetArrayAccessProperty
     */
    public function testArrayAccessNullOffsetSet()
    {
        $this->testClass[] = 'well-done';

        $this->assertEquals('well-done', array_pop($this->testClass->options));
    }

    /**
     * @depends testGetArrayAccessProperty
     */
    public function testArrayAccessOffsetExists()
    {
        $this->assertTrue(isset($this->testClass->options['sauce']));
    }

    /**
     * @depends testGetArrayAccessProperty
     * @depends testArrayAccessOffsetExists
     */
    public function testArrayAccessOffsetGet()
    {
        $this->assertEquals('red', $this->testClass->options['sauce']);
    }

    /**
     * @depends testGetArrayAccessProperty
     */
    public function testArrayAccessNamedOffsetSet()
    {
        $this->testClass['sauce'] = 'garlic';

        $this->assertEquals('garlic', $this->testClass->options['sauce']);
    }

    /**
     * @depends testGetArrayAccessProperty
     * @depends testArrayAccessOffsetGet
     * @depends testArrayAccessOffsetExists
     */
    public function testArrayAccessOffsetUnset()
    {
        unset($this->testClass->options['toppings']);

        $this->assertFalse(isset($this->testClass->options['toppings']));
    }
}
