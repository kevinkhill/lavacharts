<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers as H;

class HelperIsIntOrPercentTest extends HelperTestCase
{

    public function testIntOrPercentWithInt()
    {
        $this->assertTrue( H::is_int_or_percent(72) );
    }

    public function testIntOrPercentWithIntAsString()
    {
        $this->assertTrue( H::is_int_or_percent('26') );
    }

    public function testIntOrPercentWithPercentAsString()
    {
        $this->assertTrue( H::is_int_or_percent('45%') );
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testIntOrPercentWithBadParams($value)
    {
        $this->assertFalse( H::is_int_or_percent($value) );
    }

    public function badParamsProvider()
    {
        return array(
            array('string'),
            array('2f3%'),
            array(123.456),
            array(array('test1')),
            array(new \stdClass()),
            array(TRUE),
            array(FALSE),
            array(NULL)
        );
    }

}
