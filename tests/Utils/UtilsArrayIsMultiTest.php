<?php

namespace Khill\Lavacharts\Tests\Utils;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Utils;

class UtilsArrayIsMultiTest extends ProvidersTestCase
{
    public function testArrayIsMultiWithMultiArray()
    {
        $multiArray = [['test1'], ['test2']];

        $this->assertTrue(Utils::arrayIsMulti($multiArray));
    }

    public function testArrayIsMultiWithNonMultiArray()
    {
        $this->assertFalse(Utils::arrayIsMulti(['test1']));
    }

    /**
     * @dataProvider nonArrayProvider
     *
     * @param $notArray
     */
    public function testArrayIsMultiWithNonArray($notArray)
    {
        $this->assertFalse(Utils::arrayIsMulti($notArray));
    }

    /**
     * @dataProvider traversableProvider
     *
     * @param $traversable
     */
    public function testArrayIsMultiWithTraversable($traversable)
    {
        $this->assertTrue(Utils::arrayIsMulti($traversable));
    }

    /**
     * @dataProvider arrayAccessProvider
     *
     * @param $arrayAccessAr
     */
    public function testArrayIsMultiWithArrayAccessArray($arrayAccessAr)
    {
        $this->assertTrue(Utils::arrayIsMulti($arrayAccessAr));
    }
}
