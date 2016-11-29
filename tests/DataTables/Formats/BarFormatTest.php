<?php

namespace Khill\Lavacharts\Tests\Formats;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Formats\BarFormat;

class BarFormatTest extends ProvidersTestCase
{
    public $barFormat;

    public $json = '{"base":10,"colorNegative":"red","colorPositive":"green","drawZeroLine":false,"max":100,"min":10,"showValue":true,"width":20}';

    public function setUp()
    {
        parent::setUp();

        $this->barFormat = new BarFormat([
            'base'          => 10,
            'colorNegative' => 'red',
            'colorPositive' => 'green',
            'drawZeroLine'  => false,
            'max'           => 100,
            'min'           => 10,
            'showValue'     => true,
            'width'         => 20
        ]);
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\BarFormat
     */
    public function testConstructorOptionAssignment()
    {
        $this->assertEquals(10, $this->barFormat['base']);
        $this->assertEquals('red', $this->barFormat['colorNegative']);
        $this->assertEquals('green', $this->barFormat['colorPositive']);
        $this->assertFalse($this->barFormat['drawZeroLine']);
        $this->assertEquals(10, $this->barFormat['min']);
        $this->assertEquals(100, $this->barFormat['max']);
        $this->assertTrue($this->barFormat['showValue']);
        $this->assertEquals(20, $this->barFormat['width']);
    }

    public function testGetType()
    {
        $this->assertEquals('BarFormat', $this->barFormat->getType());
    }

    public function testToJsonForOptionsSerialization()
    {
        $this->assertEquals($this->json, $this->barFormat->toJson());
    }

    /**
     * @depends testToJsonForOptionsSerialization
     */
    public function testToJavascriptForObjectSerialization()
    {
        $javascript = 'google.visualization.BarFormat('.$this->json.')';

        $this->assertEquals($javascript, $this->barFormat->toJavascript());
    }
}
