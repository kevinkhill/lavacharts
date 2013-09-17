<?php namespace Khill\Lavacharts;

use Khill\Lavacharts\Helpers\Helpers as H;

class HelperIsIntOrPercentTest extends TestCase\HelperTestCase
{

    public function testIntOrPercentWithInt()
    {
        $this->assertTrue( H::is_int_or_percent(72) );
    }

    public function testIntOrPercentWithPercentAsString()
    {
        $this->assertTrue( H::is_int_or_percent('45%') );
    }

    public function testIntOrPercentWithBadParam()
    {
        $this->assertFalse( H::is_int_or_percent(array('test1')) );
    }

}
