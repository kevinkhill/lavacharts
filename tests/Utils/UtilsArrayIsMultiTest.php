<?php namespace Khill\Lavacharts\Tests\Helpers;

use \Khill\Lavacharts\Utils;

class HelperArrayIsMultiTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayIsMultiWithMultiArray()
    {
        $multiArray = array( array('test1'), array('test2') );

        $this->assertTrue(Utils::arrayIsMulti( $multiArray ) );
    }

    public function testArrayIsMultiWithNonMultiArray()
    {
        $this->assertFalse(Utils::arrayIsMulti( array('test1') ) );
    }

    public function testArrayIsMultiWithNonArray()
    {
        $this->assertFalse(Utils::arrayIsMulti( 'test1' ) );
    }
}
