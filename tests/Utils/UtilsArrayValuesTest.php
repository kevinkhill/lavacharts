<?php namespace Khill\Lavacharts\Tests\Utilss;

use \Mockery as m;
use \Khill\Lavacharts\Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayValuesCheckWithStrings()
    {
        $testArray = array('test1', 'test2', 'test3');
        $this->assertTrue(Utils::arrayValuesCheck($testArray, 'string'));
    }

    public function testArrayValuesCheckWithInts()
    {
        $testArray = array(1, 2, 3, 4, 5);
        $this->assertTrue(Utils::arrayValuesCheck($testArray, 'int'));
    }

    public function testArrayValuesCheckWithFloats()
    {
        $testArray = array(1.1, 2.2, 3.3, 4.4, 5.5);
        $this->assertTrue(Utils::arrayValuesCheck($testArray, 'float'));
    }

    public function testArrayValuesCheckWithBools()
    {
        $testArray = array(true, false, true, false);
        $this->assertTrue(Utils::arrayValuesCheck($testArray, 'bool'));
    }

    public function testArrayValuesCheckWithObjects()
    {
        $testArray = array(new \stdClass, new \stdClass, new \stdClass);
        $this->assertTrue(Utils::arrayValuesCheck($testArray, 'object'));
    }

    public function testArrayValuesCheckWithConfigObjects()
    {
        $mca = m::mock('Khill\Lavacharts\Configs\ColorAxis');
        //$mca->className = 'ColorAxis';

        $testArray = array($mca, $mca, $mca);

        $this->assertTrue(Utils::arrayValuesCheck($testArray, 'class', 'ColorAxis'));
    }

    public function testArrayValuesCheckWithConfigObjectsAndNulls()
    {
        $mca = m::mock('Khill\Lavacharts\Configs\ColorAxis');
        //$mca->className = 'ColorAxis';

        $testArray = array(null, $mca, null);

        $this->assertTrue(Utils::arrayValuesCheck($testArray, 'class', 'ColorAxis'));
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testArrayValuesCheckWithBadParams($badData, $testType, $extra = '')
    {
        $this->assertFalse(Utils::arrayValuesCheck($badData, $testType, $extra));
    }


    public function badParamsProvider()
    {
        $mca = m::mock('Khill\Lavacharts\Configs\ColorAxis');

        return array(
            array('string', 'stringy'),
            array(array(1, 2, 3), 'tacos'),
            array(array(1, 2, 'blahblah', 3), 'int'),
            array(array('taco', new \stdClass, 1), 'class', 'burrito'),
            array(array($mca, $mca), 'class', 'helicopters'),
            array(array(true, true), 4),
            array(array(false, false), 'boolean'),
            array(array(null, null), 'tacos')
        );
    }
}
