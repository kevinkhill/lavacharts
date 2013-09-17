<?php namespace Khill\Lavacharts;

use Khill\Lavacharts\Helpers\Helpers as H;

class HelperTest extends TestCase\HelperTestCase
{

    public function testArrayValuesCheckWithNonArrayAndString()
    {
        $testArray = array('test1', 'test2', 'test3');
        $this->assertFalse( H::array_values_check('notArray', 'string') );
    }

    public function testArrayValuesCheckWithNonArrayAndNonString()
    {
        $testArray = array('test1', 'test2', 'test3');
        $this->assertFalse( H::array_values_check('notArray', 4) );
    }

    public function testArrayValuesCheckWithStrings()
    {
        $testArray = array('test1', 'test2', 'test3');
        $this->assertTrue( H::array_values_check($testArray, 'string' ) );
    }

    public function testArrayValuesCheckWithInts()
    {
        $testArray = array(1, 2, 3, 4, 5);
        $this->assertTrue( H::array_values_check($testArray, 'int' ) );
    }

    public function testArrayValuesCheckWithObjects()
    {
        $testArray = array($this->obj, $this->obj, $this->obj);
        $this->assertTrue( H::array_values_check($testArray, 'object' ) );
    }

    public function testArrayValuesCheckWithConfigObjects()
    {
        $testArray = array($this->textStyle, $this->textStyle, $this->textStyle);
        $this->assertTrue( H::array_values_check($testArray, 'class', 'textStyle' ) );
    }

}
