<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers as h;

class HelperArrayToPipedStringTest extends \PHPUnit_Framework_TestCase
{

    public function testArrayStringWithArray()
    {
        $actual = h::arrayToPipedString(array('test1', 'test2'));
        $expected = '[ test1 | test2 ]';

        $this->assertEquals($actual, $expected);
    }

    public function testArrayStringWithNonArray()
    {
        $this->assertFalse(h::arrayToPipedString( 'test1' ) );
    }

}
