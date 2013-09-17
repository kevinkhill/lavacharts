<?php namespace Khill\Lavacharts;

use Khill\Lavacharts\Helpers\Helpers as H;

class HelperArrayStringTest extends TestCase\HelperTestCase
{

    public function testArrayStringWithArray()
    {
        $actual = H::array_string(array('test1', 'test2'));
        $expected = '[ test1 | test2 ]';

        $this->assertEquals($actual, $expected);
    }

    public function testArrayStringWithNonArray()
    {
        $this->assertFalse( H::array_string( 'test1' ) );
    }

}
