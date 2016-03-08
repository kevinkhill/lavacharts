<?php

namespace Khill\Lavacharts\Tests\Formats;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Formats\NumberFormat;

class NumberFormatTest extends ProvidersTestCase
{
    public $numberFormat;

    public $json = '{"decimalSymbol":".","fractionDigits":2,"groupingSymbol":",","negativeColor":"red","negativeParens":true,"pattern":"#,###","prefix":"$","suffix":"\/hr"}';

    public function setUp()
    {
        parent::setUp();

        $this->numberFormat = new NumberFormat([
            'decimalSymbol'  => '.',
            'fractionDigits' => 2,
            'groupingSymbol' => ',',
            'negativeColor'  => 'red',
            'negativeParens' => true,
            'pattern'        => '#,###',
            'prefix'         => '$',
            'suffix'         => '/hr'
        ]);
    }

        /**
     * @covers \Khill\Lavacharts\DataTables\Formats\NumberFormat
     */
    public function testConstructorOptionAssignment()
    {
        $this->assertEquals('.', $this->numberFormat['decimalSymbol']);
        $this->assertEquals(2, $this->numberFormat['fractionDigits']);
        $this->assertEquals(',', $this->numberFormat['groupingSymbol']);
        $this->assertEquals('red', $this->numberFormat['negativeColor']);
        $this->assertTrue($this->numberFormat['negativeParens']);
        $this->assertEquals('#,###', $this->numberFormat['pattern']);
        $this->assertEquals('$', $this->numberFormat['prefix']);
        $this->assertEquals('/hr', $this->numberFormat['suffix']);
    }

    public function testGetType()
    {
        $numberFormat = new NumberFormat;

        $this->assertEquals('NumberFormat', $numberFormat->getType());
    }

    public function testToJsonForOptionsSerialization()
    {
        $this->assertEquals($this->json, $this->numberFormat->toJson());
    }

    /**
     * @depends testToJsonForOptionsSerialization
     */
    public function testToJavascriptForObjectSerialization()
    {
        $javascript = 'google.visualization.NumberFormat('.$this->json.')';

        $this->assertEquals($javascript, $this->numberFormat->toJavascript());
    }
}
