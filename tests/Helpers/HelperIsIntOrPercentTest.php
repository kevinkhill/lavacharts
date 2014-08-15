<?php namespace Lavacharts\Tests\Helpers;

use \Lavacharts\Helpers\Helpers as h;

class HelperIsIntOrPercentTest extends \PHPUnit_Framework_TestCase
{
    public function testIntOrPercentWithInt()
    {
        $this->assertTrue(h::isIntOrPercent(72) );
    }

    public function testIntOrPercentWithIntAsString()
    {
        $this->assertTrue(h::isIntOrPercent('26') );
    }

    public function testIntOrPercentWithPercentAsString()
    {
        $this->assertTrue(h::isIntOrPercent('45%') );
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testIntOrPercentWithBadParams($value)
    {
        $this->assertFalse(h::isIntOrPercent($value) );
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
