<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers as H;

class HelperArrayIsMultiTest extends HelperTestCase
{

    public function testArrayIsMultiWithMultiArray()
    {
        $multiArray = array( array('test1'), array('test2') );

        $this->assertTrue( H::array_is_multi( $multiArray ) );
    }

    public function testArrayIsMultiWithNonMultiArray()
    {
        $this->assertFalse( H::array_is_multi( array('test1') ) );
    }

    public function testArrayIsMultiWithNonArray()
    {
        $this->assertFalse( H::array_is_multi( 'test1' ) );
    }

}
