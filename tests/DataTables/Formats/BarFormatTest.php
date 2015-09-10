<?php

namespace Khill\Lavacharts\Tests\Formats;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Formats\BarFormat;

class BarFormatTest extends ProvidersTestCase
{
    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\BarFormat
     */
    public function testConstructorArgs()
    {
        $barFormat = new BarFormat([
            'base'          => 10,
            'colorNegative' => 'red',
            'colorPositive' => 'green',
            'drawZeroLine'  => false,
            'max'           => 100,
            'min'           => 10,
            'showValue'     => true,
            'width'         => 20
        ]);

        $this->assertEquals(10, $barFormat->base);
        $this->assertEquals('red', $barFormat->colorNegative);
        $this->assertEquals('green', $barFormat->colorPositive);
        $this->assertFalse($barFormat->drawZeroLine);
        $this->assertEquals(10, $barFormat->min);
        $this->assertEquals(100, $barFormat->max);
        $this->assertTrue($barFormat->showValue);
        $this->assertEquals(20, $barFormat->width);
    }

    public function testGetType()
    {
        $barFormat = new BarFormat;

        $this->assertEquals('BarFormat', BarFormat::TYPE);
        $this->assertEquals('BarFormat', $barFormat->getType());
    }
}
