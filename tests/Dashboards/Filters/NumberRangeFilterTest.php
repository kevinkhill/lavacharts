<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\NumberRange;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class NumberRangeFilterTest extends ProvidersTestCase
{
    public function testSettingColumnIndexWithConstructor()
    {
        $numberRangeFilter = new NumberRange(2);

        $this->assertEquals(2, $numberRangeFilter->filterColumnIndex);
    }

    public function testSettingColumnLabelWithConstructor()
    {
        $numberRangeFilter = new NumberRange('donuts');

        $this->assertEquals('donuts', $numberRangeFilter->filterColumnLabel);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testGetTypeMethodAndStaticReferences()
    {
        $numberRangeFilter = new NumberRange('donuts');

        $this->assertEquals('NumberRangeFilter', NumberRange::TYPE);
        $this->assertEquals('NumberRangeFilter', $numberRangeFilter::TYPE);
        $this->assertEquals('NumberRangeFilter', $numberRangeFilter->getType());
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testMinValueWithInt()
    {
        $numberRangeFilter = new NumberRange('donuts');

        $numberRangeFilter->minValue(3);
        $this->assertEquals(3, $numberRangeFilter->minValue);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testMinValueWithFloat()
    {
        $numberRangeFilter = new NumberRange('donuts');

        $numberRangeFilter->minValue(4.5);
        $this->assertEquals(4.5, $numberRangeFilter->minValue);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMinValueWithBadTypes($badVals)
    {
        $numberRangeFilter = new NumberRange('donuts');

        $numberRangeFilter->minValue($badVals);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testMaxValueWithInt()
    {
        $numberRangeFilter = new NumberRange('donuts');

        $numberRangeFilter->maxValue(3);
        $this->assertEquals(3, $numberRangeFilter->maxValue);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testMaxValueWithFloat()
    {
        $numberRangeFilter = new NumberRange('donuts');

        $numberRangeFilter->maxValue(4.5);
        $this->assertEquals(4.5, $numberRangeFilter->maxValue);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @dataProvider nonNumericProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testMaxValueWithBadTypes($badVals)
    {
        $numberRangeFilter = new NumberRange('donuts');

        $numberRangeFilter->maxValue($badVals);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testUi()
    {
        $numberRangeFilter = new NumberRange('donuts');
        $numberRangeFilter->ui([
            'format' => [
                'decimalSymbol' => '.'
            ]
        ]);

        $this->assertInstanceOf('Khill\Lavacharts\Configs\UIs\NumberRangeUI', $numberRangeFilter->ui);
    }
}
