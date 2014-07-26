<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers as h;
use Mockery as m;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayValuesCheckWithStrings()
    {
        $testArray = array('test1', 'test2', 'test3');
        $this->assertTrue(h::arrayValuesCheck($testArray, 'string'));
    }

    public function testArrayValuesCheckWithInts()
    {
        $testArray = array(1, 2, 3, 4, 5);
        $this->assertTrue(h::arrayValuesCheck($testArray, 'int'));
    }

    public function testArrayValuesCheckWithFloats()
    {
        $testArray = array(1.1, 2.2, 3.3, 4.4, 5.5);
        $this->assertTrue(h::arrayValuesCheck($testArray, 'float'));
    }

    public function testArrayValuesCheckWithBools()
    {
        $testArray = array(TRUE, FALSE, TRUE, FALSE);
        $this->assertTrue(h::arrayValuesCheck($testArray, 'bool'));
    }

    public function testArrayValuesCheckWithObjects()
    {
        $testArray = array(new \stdClass, new \stdClass, new \stdClass);
        $this->assertTrue(h::arrayValuesCheck($testArray, 'object'));
    }

    public function testArrayValuesCheckWithConfigObjects()
    {
        $mockColorAxis = m::mock('Khill\Lavacharts\Configs\ColorAxis');

        $testArray = array($mockColorAxis, $mockColorAxis, $mockColorAxis);

        $this->assertTrue(h::arrayValuesCheck($testArray, 'class', 'ColorAxis'));
    }

    public function testArrayValuesCheckWithConfigObjectsAndNulls()
    {
        $mockColorAxis = m::mock('Khill\Lavacharts\Configs\ColorAxis');
        $mockColorAxis->className = 'ColorAxis';

        $testArray = array(null, $mockColorAxis, null);

        $this->assertTrue(h::arrayValuesCheck($testArray, 'class', 'ColorAxis'));
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testArrayValuesCheckWithBadParams($badData, $testType, $extra = '')
    {
        $this->assertFalse(h::arrayValuesCheck($badData, $testType, $extra));
    }


    public function badParamsProvider()
    {
        $mockColorAxis = m::mock('Khill\Lavacharts\Configs\ColorAxis');

        return array(
            array('string', 'stringy'),
            array(array(1, 2, 3), 'tacos'),
            array(array(1, 2, 'blahblah', 3), 'int'),
            array(array('taco', new \stdClass, 1), 'class', 'burrito'),
            array(array($mockColorAxis, $mockColorAxis), 'class', 'helicopters'),
            array(array(TRUE, TRUE), 4),
            array(array(FALSE, FALSE), 'boolean'),
            array(array(NULL, NULL), 'tacos')
        );
    }
}
