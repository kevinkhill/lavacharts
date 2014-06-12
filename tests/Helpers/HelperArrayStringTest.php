<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Tests\TestCase;
use Khill\Lavacharts\Helpers\Helpers;

class HelperArrayToPipedStringTest extends TestCase
{

    public function testArrayStringWithArray()
    {
        $actual = Helpers::arrayToPipedString(array('test1', 'test2'));
        $expected = '[ test1 | test2 ]';

        $this->assertEquals($actual, $expected);
    }

    public function testArrayStringWithNonArray()
    {
        $this->assertFalse( Helpers::arrayToPipedString( 'test1' ) );
    }

}
