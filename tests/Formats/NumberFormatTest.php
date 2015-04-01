<?php namespace Khill\Lavacharts\Tests\Formats;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use \Khill\Lavacharts\Formats\NumberFormat;

class NumberFormatTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->numberFormat = new NumberFormat;
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('\\Khill\\Lavacharts\\Formats\\NumberFormat', $this->numberFormat);
    }

    public function testStaticFormatType()
    {
        $this->assertEquals('NumberFormat', NumberFormat::TYPE);
    }

    public function testDecimalSymbolWithString()
    {
        $this->numberFormat->decimalSymbol('.');
        $this->assertEquals('.', $this->numberFormat->decimalSymbol);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testDecimalSymbolWithBadTypes($badTypes)
    {
        $this->numberFormat->decimalSymbol($badTypes);
    }

    public function testFractionDigitsWithInt()
    {
        $this->numberFormat->fractionDigits(2);
        $this->assertEquals(2, $this->numberFormat->fractionDigits);
    }

    /**
     * @dataProvider nonNumericProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFractionDigitsWithBadTypes($badTypes)
    {
        $this->numberFormat->fractionDigits($badTypes);
    }

    public function testGroupingSymbolWithString()
    {
        $this->numberFormat->groupingSymbol(',');
        $this->assertEquals(',', $this->numberFormat->groupingSymbol);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testGroupingSymbolWithBadTypes($badTypes)
    {
        $this->numberFormat->groupingSymbol($badTypes);
    }

    public function testNegativeColorWithString()
    {
        $this->numberFormat->negativeColor('red');
        $this->assertEquals('red', $this->numberFormat->negativeColor);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testNegativeColorWithBadTypes($badTypes)
    {
        $this->numberFormat->negativeColor($badTypes);
    }

    public function testNegativeParensWithBool()
    {
        $this->numberFormat->negativeParens(true);
        $this->assertTrue($this->numberFormat->negativeParens);
    }

    /**
     * @dataProvider nonBoolProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testNegativeParensWithBadTypes($badTypes)
    {
        $this->numberFormat->negativeParens($badTypes);
    }

    public function testPatternWithString()
    {
        $this->numberFormat->pattern('#,###');
        $this->assertEquals('#,###', $this->numberFormat->pattern);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPatternWithBadTypes($badTypes)
    {
        $this->numberFormat->pattern($badTypes);
    }

    public function testPrefixWithString()
    {
        $this->numberFormat->prefix('$');
        $this->assertEquals('$', $this->numberFormat->prefix);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testPrefixWithBadTypes($badTypes)
    {
        $this->numberFormat->prefix($badTypes);
    }

    public function testSuffixWithString()
    {
        $this->numberFormat->suffix('%');
        $this->assertEquals('%', $this->numberFormat->suffix);
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testSuffixWithBadTypes($badTypes)
    {
        $this->numberFormat->suffix($badTypes);
    }
}
