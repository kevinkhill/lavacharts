<?php namespace Khill\Lavacharts\Tests\Helpers;

use \Khill\Lavacharts\Utils;

class HelperIsIntOrPercentTest extends \PHPUnit_Framework_TestCase
{
    public function testIntOrPercentWithInt()
    {
        $this->assertTrue(Utils::isIntOrPercent(72) );
    }

    public function testIntOrPercentWithIntAsString()
    {
        $this->assertTrue(Utils::isIntOrPercent('26') );
    }

    public function testIntOrPercentWithPercentAsString()
    {
        $this->assertTrue(Utils::isIntOrPercent('45%') );
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testIntOrPercentWithBadParams($value)
    {
        $this->assertFalse(Utils::isIntOrPercent($value) );
    }


    public function badParamsProvider()
    {
        return array(
            array('string'),
            array('2f3%'),
            array(123.456),
            array(array('test1')),
            array(new \stdClass),
            array(TRUE),
            array(FALSE),
            array(NULL)
        );
    }
}
