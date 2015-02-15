<?php namespace Khill\Lavacharts\Tests\Utilss;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Utils;

class UtilsArrayIsMultiTest extends ProvidersTestCase
{
    public function testArrayIsMultiWithMultiArray()
    {
        $multiArray = array(array('test1'), array('test2'));

        $this->assertTrue(Utils::arrayIsMulti($multiArray));
    }

    public function testArrayIsMultiWithNonMultiArray()
    {
        $this->assertFalse(Utils::arrayIsMulti(array('test1')));
    }

    /**
     * @dataProvider nonArrayProvider
     */
    public function testArrayIsMultiWithNonArray($notArray)
    {
        $this->assertFalse(Utils::arrayIsMulti($notArray));
    }
}
