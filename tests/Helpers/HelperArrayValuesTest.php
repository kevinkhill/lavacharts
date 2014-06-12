<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Tests\TestCase;
use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Configs\TextStyle;

class HelperTest extends TestCase
{

    public function testArrayValuesCheckWithStrings()
    {
        $testArray = array('test1', 'test2', 'test3');
        $this->assertTrue( Helpers::arrayValuesCheck($testArray, 'string') );
    }

    public function testArrayValuesCheckWithInts()
    {
        $testArray = array(1, 2, 3, 4, 5);
        $this->assertTrue( Helpers::arrayValuesCheck($testArray, 'int') );
    }

    public function testArrayValuesCheckWithBools()
    {
        $testArray = array(TRUE, FALSE, TRUE, FALSE);
        $this->assertTrue( Helpers::arrayValuesCheck($testArray, 'bool') );
    }

    public function testArrayValuesCheckWithObjects()
    {
        $testArray = array(new \stdClass, new \stdClass, new \stdClass);
        $this->assertTrue( Helpers::arrayValuesCheck($testArray, 'object') );
    }

    public function testArrayValuesCheckWithConfigObjects()
    {
        $testArray = array(new TextStyle, new TextStyle, new TextStyle);

        $this->assertTrue( Helpers::arrayValuesCheck($testArray, 'class', 'TextStyle') );
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testArrayValuesCheckWithBadParams($badData, $testType, $extra = '')
    {
        $this->assertFalse( Helpers::arrayValuesCheck($badData, $testType, $extra) );
    }


    public function badParamsProvider()
    {
        return array(
            array('string', 'stringy'),
            array(array(1, 2, 3), 'tacos'),
            array(array(1, 2, 'blahblah', 3), 'int'),
            array(array('taco', new \stdClass, 1), 'class', 'burrito'),
            array(array(new TextStyle, new TextStyle, new \DateTime), 'class', 'TextStyle'),
            array(array(TRUE, TRUE), 4),
            array(array(FALSE, FALSE), 'boolean'),
            array(array(NULL, NULL), 'tacos')
        );
    }

}
