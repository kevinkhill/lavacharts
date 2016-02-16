<?php

namespace Khill\Lavacharts\Tests\Formats;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\DataTables\Formats\NumberFormat;

class NumberFormatTest extends ProvidersTestCase
{
    /**
     * @covers \Khill\Lavacharts\DataTables\Formats\NumberFormat
     */
    public function testInstanceOf()
    {
        $numberFormat = new NumberFormat([
            'decimalSymbol'  => '.',
            'fractionDigits' => 2,
            'groupingSymbol' => ',',
            'negativeColor'  => 'red',
            'negativeParens' => true,
            'pattern'        => '#,###',
            'prefix'         => '$',
            'suffix'         => '/hr'
        ]);

        $numberFormatOptions = $numberFormat->getOptions();

        $this->assertEquals('.', $numberFormatOptions['decimalSymbol']);
        $this->assertEquals(2, $numberFormatOptions['fractionDigits']);
        $this->assertEquals(',', $numberFormatOptions['groupingSymbol']);
        $this->assertEquals('red', $numberFormatOptions['negativeColor']);
        $this->assertTrue($numberFormatOptions['negativeParens']);
        $this->assertEquals('#,###', $numberFormatOptions['pattern']);
        $this->assertEquals('$', $numberFormatOptions['prefix']);
        $this->assertEquals('/hr', $numberFormatOptions['suffix']);
    }

    public function testGetType()
    {
        $numberFormat = new NumberFormat;

        $this->assertEquals('NumberFormat', NumberFormat::TYPE);
        $this->assertEquals('NumberFormat', $numberFormat->getType());
    }
}
