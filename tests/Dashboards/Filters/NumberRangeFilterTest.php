<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\NumberRangeFilter;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class NumberRangeFilterTest extends ProvidersTestCase
{
    public function testSettingColumnIndexWithConstructor()
    {
        $numberRangeFilter = new NumberRangeFilter(2);

        $this->assertEquals(2, $numberRangeFilter->filterColumnIndex);
    }

    public function testSettingColumnLabelWithConstructor()
    {
        $numberRangeFilter = new NumberRangeFilter('donuts');

        $this->assertEquals('donuts', $numberRangeFilter->filterColumnLabel);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @covers \Khill\Lavacharts\Dashboards\Filters\NumberRangeFilter::getType
     */
    public function testGetType()
    {
        $numberRangeFilter = new NumberRangeFilter('donuts');

        $this->assertEquals('NumberRangeFilter', $numberRangeFilter->getType());
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testMinValueWithInt()
    {
        $numberRangeFilter = new NumberRangeFilter('donuts');

        $numberRangeFilter->minValue(3);
        $this->assertEquals(3, $numberRangeFilter->minValue);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testMinValueWithFloat()
    {
        $numberRangeFilter = new NumberRangeFilter('donuts');

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
        $numberRangeFilter = new NumberRangeFilter('donuts');

        $numberRangeFilter->minValue($badVals);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testMaxValueWithInt()
    {
        $numberRangeFilter = new NumberRangeFilter('donuts');

        $numberRangeFilter->maxValue(3);
        $this->assertEquals(3, $numberRangeFilter->maxValue);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testMaxValueWithFloat()
    {
        $numberRangeFilter = new NumberRangeFilter('donuts');

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
        $numberRangeFilter = new NumberRangeFilter('donuts');

        $numberRangeFilter->maxValue($badVals);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     */
    public function testUi()
    {
        $numberRangeFilter = new NumberRangeFilter('donuts');
        $numberRangeFilter->ui([
            'format' => [
                'decimalSymbol' => '.'
            ]
        ]);

        $this->assertInstanceOf('Khill\Lavacharts\Configs\UIs\NumberRangeUI', $numberRangeFilter->ui);
    }
}
