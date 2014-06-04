<?php namespace Khill\Lavacharts\Tests\Helpers;

use Khill\Lavacharts\Helpers\Helpers as H;

class HelperGetRealClassTest extends HelperTestCase
{

    public function testGetRealClassFromConfigObject()
    {
        $actual = H::get_real_class($this->textStyle);
        $expected = 'textStyle';

        $this->assertEquals($actual, $expected);
    }

    /**
     * @dataProvider badParamsProvider
     */
    public function testGetRealClassWithBadParam($badParams)
    {
        $this->assertFalse( H::get_real_class($badParams) );
    }

    public function badParamsProvider()
    {
        return array(
            array('string'),
            array(123),
            array(123.456),
            array(array()),
            array(TRUE),
            array(FALSE),
            array(NULL)
        );
    }

}
