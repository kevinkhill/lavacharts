<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\ChartRangeFilter;
use \Khill\Lavacharts\Tests\ProvidersTestCase;

class ChartRangeFilterTest extends ProvidersTestCase
{
    public function testSettingColumnIndexWithConstructor()
    {
        $chartRangeFilter = new ChartRangeFilter(2);

        $this->assertEquals(2, $chartRangeFilter->filterColumnIndex);
    }

    public function testSettingColumnLabelWithConstructor()
    {
        $chartRangeFilter = new ChartRangeFilter('lines');

        $this->assertEquals('lines', $chartRangeFilter->filterColumnLabel);
    }

    /**
     * @depends testSettingColumnLabelWithConstructor
     * @covers \Khill\Lavacharts\Dashboards\Filters\ChartRangeFilter::getType
     */
    public function testGetType()
    {
        $chartRangeFilter = new ChartRangeFilter('donuts');

        $this->assertEquals('ChartRangeFilter', $chartRangeFilter->getType());
    }
}
