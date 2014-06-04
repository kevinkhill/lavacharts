<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers as H;
use \Mockery as M;

class HelperTest extends HelperTestCase
{

    public function tearDown()
    {
       M::close();
    }

    public function testArrayValuesCheckWithStrings()
    {
        $testArray = array('test1', 'test2', 'test3');
        $this->assertTrue( H::array_values_check($testArray, 'string') );
    }

    public function testArrayValuesCheckWithInts()
    {
        $testArray = array(1, 2, 3, 4, 5);
        $this->assertTrue( H::array_values_check($testArray, 'int') );
    }

    public function testArrayValuesCheckWithBools()
    {
        $testArray = array(TRUE, FALSE, TRUE, FALSE);
        $this->assertTrue( H::array_values_check($testArray, 'bool') );
    }

    public function testArrayValuesCheckWithObjects()
    {
        $testArray = array($this->obj, $this->obj, $this->obj);
        $this->assertTrue( H::array_values_check($testArray, 'object') );
    }

    public function testArrayValuesCheckWithConfigObjects()
    {
        $testArray = array($this->textStyle, $this->textStyle, $this->textStyle);
        $this->assertTrue( H::array_values_check($testArray, 'class', 'textStyle') );
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testArrayValuesCheckWithBadParams($testArray, $testAgainst, $extra = '')
    {
        $this->assertFalse( H::array_values_check($testArray, $testAgainst, $extra) );
    }

    public function badParamsProvider()
    {
        $textStyle = M::mock('textStyle');

        return array(
            array('string', 'string'),
            array(array(1,2,3), 'tacos'),
            array(array(1,2,3), 'class', 'tacos'),
            array(array(1,2,3, 'string'), 'int'),
            array(array('taco', new \stdClass(), 1), 'class', 'burrito'),
            array(array($textStyle, $textStyle), 'class', 'textStyle'),
            array(array(TRUE, TRUE), 4),
            array(array(FALSE, FALSE), 'boolean'),
            array(array(NULL, NULL), 'tacos')
        );
    }

}
