<?php namespace Khill\Lavacharts\Tests\Helpers;

use \Khill\Lavacharts\Utils;

class HelperArrayToPipedStringTest extends \PHPUnit_Framework_TestCase
{

    public function testArrayStringWithArray()
    {
        $actual = Utils::arrayToPipedString(array('test1', 'test2'));
        $expected = '[ test1 | test2 ]';

        $this->assertEquals($actual, $expected);
    }

    public function testArrayStringWithNonArray()
    {
        $this->assertFalse(Utils::arrayToPipedString( 'test1' ) );
    }

}
