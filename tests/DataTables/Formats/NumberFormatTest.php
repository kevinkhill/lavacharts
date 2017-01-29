<?php

namespace Khill\Lavacharts\Tests\Formats;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Formats\NumberFormat;

/**
 * @property \Khill\Lavacharts\DataTables\Formats\NumberFormat numberFormat
 */
class NumberFormatTest extends ProvidersTestCase
{
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

    public function testGetJsClass()
    {
        $jsClass = 'google.visualization.NumberFormat';

        $this->assertEquals($jsClass, $this->numberFormat->getJsClass());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\DateFormat::toJson()
     */
    public function testToJson()
    {
        $this->assertEquals($this->json, $this->numberFormat->toJson());
    }

    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\DateFormat::jsonSerialize()
     */
    public function testJsonSerialization()
    {
        $this->assertEquals($this->json, json_encode($this->numberFormat));
    }
}
