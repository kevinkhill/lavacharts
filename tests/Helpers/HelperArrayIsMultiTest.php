<?php namespace Lavacharts\Tests\Helpers;

use \Lavacharts\Helpers\Helpers as h;

class HelperArrayIsMultiTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayIsMultiWithMultiArray()
    {
        $multiArray = array( array('test1'), array('test2') );

        $this->assertTrue(h::arrayIsMulti( $multiArray ) );
    }

    public function testArrayIsMultiWithNonMultiArray()
    {
        $this->assertFalse(h::arrayIsMulti( array('test1') ) );
    }

    public function testArrayIsMultiWithNonArray()
    {
        $this->assertFalse(h::arrayIsMulti( 'test1' ) );
    }
}
