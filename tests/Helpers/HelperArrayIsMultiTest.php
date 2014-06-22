<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers;

class HelperArrayIsMultiTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayIsMultiWithMultiArray()
    {
        $multiArray = array( array('test1'), array('test2') );

        $this->assertTrue( Helpers::arrayIsMulti( $multiArray ) );
    }

    public function testArrayIsMultiWithNonMultiArray()
    {
        $this->assertFalse( Helpers::arrayIsMulti( array('test1') ) );
    }

    public function testArrayIsMultiWithNonArray()
    {
        $this->assertFalse( Helpers::arrayIsMulti( 'test1' ) );
    }
}
