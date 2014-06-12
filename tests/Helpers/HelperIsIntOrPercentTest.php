<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Tests\TestCase;
use Khill\Lavacharts\Helpers\Helpers;

class HelperIsIntOrPercentTest extends TestCase
{

    public function testIntOrPercentWithInt()
    {
        $this->assertTrue( Helpers::isIntOrPercent(72) );
    }

    public function testIntOrPercentWithIntAsString()
    {
        $this->assertTrue( Helpers::isIntOrPercent('26') );
    }

    public function testIntOrPercentWithPercentAsString()
    {
        $this->assertTrue( Helpers::isIntOrPercent('45%') );
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testIntOrPercentWithBadParams($value)
    {
        $this->assertFalse( Helpers::isIntOrPercent($value) );
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
