<?php namespace Khill\Lavacharts;

use Khill\Lavacharts\Helpers\Helpers as H;

class HelperGetRealClassTest extends TestCase\HelperTestCase
{

    public function testGetRealClassFromConfigObject()
    {
        $actual = H::get_real_class($this->textStyle);
        $expected = 'textStyle';

        $this->assertEquals($actual, $expected);
    }

    public function testGetRealClassFromNonObject()
    {
        $this->assertFalse( H::get_real_class('tacos') );
    }

}
