<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers as H;

class HelperArrayStringTest extends HelperTestCase
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
