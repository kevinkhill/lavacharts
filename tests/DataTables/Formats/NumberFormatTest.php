<?php

namespace Khill\Lavacharts\Tests\Formats;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Formats\NumberFormat;

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

        $this->assertEquals('.', $numberFormat->decimalSymbol);
        $this->assertEquals(2, $numberFormat->fractionDigits);
        $this->assertEquals(',', $numberFormat->groupingSymbol);
        $this->assertEquals('red', $numberFormat->negativeColor);
        $this->assertTrue($numberFormat->negativeParens);
        $this->assertEquals('#,###', $numberFormat->pattern);
        $this->assertEquals('$', $numberFormat->prefix);
        $this->assertEquals('/hr', $numberFormat->suffix);
    }

    public function testGetType()
    {
        $numberFormat = new NumberFormat;

        $this->assertEquals('NumberFormat', NumberFormat::TYPE);
        $this->assertEquals('NumberFormat', $numberFormat->getType());
    }
}
